<?php

namespace App\Services\Api;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\FcmNotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function createOrder(User $buyerUser, array $data): Order
    {
        $sellerId = (int) $data['seller_id'];
        $items    = $data['items'];

        $productIds = array_column($items, 'product_id');
        $products   = Product::whereIn('id', $productIds)
            ->where('seller_id', $sellerId)
            ->get();

        if ($products->count() !== count(array_unique($productIds))) {
            throw new \RuntimeException('All products must belong to the same seller and be valid.');
        }

        $productMap = $products->keyBy('id');

        return DB::transaction(function () use ($data, $items, $productMap) {
            $order = Order::create([
                'status'         => Order::STATUS_PENDING,
                'payment_method' => $data['payment_method'],
                'buyer_id'       => $data['buyer_id'],
                'seller_id'      => $data['seller_id'],
            ]);

            foreach ($items as $item) {
                $product = $productMap->get($item['product_id']);

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => (int) $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            $order = $order->load(['orderItems.product', 'buyer', 'seller']);

            ActivityLog::log('order.created', $order, [
                'buyer_id'  => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'items'     => $order->orderItems->count(),
            ]);

            return $order;
        });

        // Notify seller: order baru masuk (di luar transaksi)
        try {
            $order->loadMissing(['seller', 'buyer']);
            if ($order->seller && $order->seller->user_id) {
                $buyerName = $order->buyer->name ?? 'Pembeli';
                app(FcmNotificationService::class)
                    ->notifyNewOrderForSeller($order->seller->user_id, $order->id, $buyerName);
            }
        } catch (\Throwable $e) {
            Log::warning('Push to seller on new order failed: ' . $e->getMessage());
        }

        return $order;
    }

    public function getPendingForSeller(User $sellerUser): Collection
    {
        $seller = $sellerUser->seller;
        if (! $seller) {
            return collect();
        }

        return Order::with(['orderItems.product', 'buyer'])
            ->where('seller_id', $seller->id)
            ->where('status', Order::STATUS_PENDING)
            ->get();
    }

    public function getHistoryForBuyer(User $buyerUser): Collection
    {
        $buyer = $buyerUser->buyer;
        if (! $buyer) {
            return collect();
        }

        return Order::with(['orderItems.product', 'seller', 'buyer'])
            ->where('buyer_id', $buyer->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getHistoryForSeller(User $sellerUser): Collection
    {
        $seller = $sellerUser->seller;
        if (! $seller) {
            return collect();
        }

        return Order::with(['orderItems.product', 'seller', 'buyer'])
            ->where('seller_id', $seller->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function accept(Order $order, User $sellerUser): Order
    {
        $this->assertSellerOwnsOrder($order, $sellerUser);

        if (! $order->canBeAccepted()) {
            throw new \RuntimeException('Order can only be accepted when status is pending.');
        }

        $order->status = Order::STATUS_ACCEPTED;
        $order->accepted_at = now();
        $order->save();

        return $order;
    }

    public function reject(Order $order, User $sellerUser, string $reason): Order
    {
        $this->assertSellerOwnsOrder($order, $sellerUser);

        if (! $order->canBeAccepted()) {
            throw new \RuntimeException('Order can only be rejected when status is pending.');
        }

        $order->status           = Order::STATUS_REJECTED;
        $order->rejection_reason = $reason;
        $order->reject_reason    = $reason;
        $order->save();

        return $order;
    }

    public function complete(Order $order, User $sellerUser): Order
    {
        $this->assertSellerOwnsOrder($order, $sellerUser);

        if (! $order->isAccepted()) {
            throw new \RuntimeException('Order can only be completed when status is accepted.');
        }

        $order->status = Order::STATUS_COMPLETED;
        $order->completed_at = now();
        $order->save();

        return $order;
    }

    public function cancelBySeller(Order $order, User $sellerUser, string $reason): Order
    {
        $this->assertSellerOwnsOrder($order, $sellerUser);

        if (! $order->canBeCancelledBySeller()) {
            throw new \RuntimeException('Order can only be cancelled by seller when status is pending or accepted.');
        }

        $order->status        = Order::STATUS_CANCELLED;
        $order->cancelled_by  = 'seller';
        $order->cancel_reason = $reason;
        $order->save();

        return $order;
    }

    public function cancelByBuyer(Order $order, User $buyerUser, ?string $reason = null): Order
    {
        $buyer = $buyerUser->buyer;
        if (! $buyer || (int) $order->buyer_id !== (int) $buyer->id) {
            throw new \RuntimeException('Forbidden');
        }

        if (! $order->canBeCancelledByBuyer()) {
            throw new \RuntimeException('Order can only be cancelled by buyer when status is pending.');
        }

        $order->status        = Order::STATUS_CANCELLED;
        $order->cancelled_by  = 'buyer';
        if ($reason !== null && $reason !== '') {
            $order->cancel_reason = $reason;
        }
        $order->save();

        return $order;
    }

    /**
     * Re-order: buat order baru berdasarkan order lama.
     * Clone item dari order sebelumnya, gunakan harga terkini produk.
     */
    public function reorder(Order $oldOrder, User $buyerUser): Order
    {
        $buyer = $buyerUser->buyer;
        if (! $buyer || (int) $oldOrder->buyer_id !== (int) $buyer->id) {
            throw new \RuntimeException('Forbidden');
        }

        $oldOrder->loadMissing('orderItems');
        if ($oldOrder->orderItems->isEmpty()) {
            throw new \RuntimeException('Order lama tidak memiliki item.');
        }

        // Kumpulkan product_id dari order lama
        $productIds = $oldOrder->orderItems->pluck('product_id')->toArray();
        $products   = Product::whereIn('id', $productIds)
            ->where('seller_id', $oldOrder->seller_id)
            ->get()
            ->keyBy('id');

        if ($products->isEmpty()) {
            throw new \RuntimeException('Tidak ada produk yang masih tersedia dari seller ini.');
        }

        // Bangun ulang items yang masih tersedia
        $items = [];
        foreach ($oldOrder->orderItems as $oi) {
            if ($products->has($oi->product_id)) {
                $items[] = [
                    'product_id' => $oi->product_id,
                    'quantity'   => $oi->quantity,
                ];
            }
        }

        if (empty($items)) {
            throw new \RuntimeException('Semua produk dari order lama sudah tidak tersedia.');
        }

        return $this->createOrder($buyerUser, [
            'seller_id'      => $oldOrder->seller_id,
            'buyer_id'       => $buyer->id,
            'payment_method' => $oldOrder->payment_method ?? Order::PAYMENT_COD,
            'items'          => $items,
        ]);
    }

    protected function assertSellerOwnsOrder(Order $order, User $sellerUser): void
    {
        $seller = $sellerUser->seller;
        if (! $seller || (int) $order->seller_id !== (int) $seller->id) {
            throw new \RuntimeException('Forbidden');
        }
    }
}

