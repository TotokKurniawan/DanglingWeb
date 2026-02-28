<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateOrderRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponse;

    public function createOrder(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        $idPedagang = (int) $validated['id_pedagang'];
        $items = $validated['items'];

        // Pastikan semua produk milik seller yang dipilih
        $productIds = array_column($items, 'product_id');
        $products = Product::whereIn('id', $productIds)->where('id_pedagang', $idPedagang)->get();
        if ($products->count() !== count(array_unique($productIds))) {
            return $this->error('Semua produk harus dari seller yang sama dan valid', 422);
        }

        $productMap = $products->keyBy('id');

        try {
            $order = DB::transaction(function () use ($validated, $items, $productMap) {
                $order = Order::create([
                    'status' => Order::STATUS_PENDING,
                    'bentuk_pembayaran' => $validated['bentuk_pembayaran'],
                    'id_pembeli' => $validated['id_pembeli'],
                    'id_pedagang' => $validated['id_pedagang'],
                ]);

                foreach ($items as $item) {
                    $product = $productMap->get($item['product_id']);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'qty' => (int) $item['qty'],
                        'harga_saat_order' => $product->harga_produk,
                    ]);
                }

                return $order->load(['orderItems.product']);
            });
        } catch (\Throwable $e) {
            return $this->error('Gagal membuat order: ' . $e->getMessage(), 500);
        }

        return $this->success(['order' => $this->formatOrder($order)], 'Order created successfully', 201);
    }

    /**
     * Format order dengan relasi untuk response API.
     */
    protected function formatOrder(Order $order): array
    {
        $order->loadMissing(['buyer', 'seller', 'orderItems.product']);
        $data = $order->toArray();
        if (isset($data['order_items'])) {
            foreach ($data['order_items'] as $i => $oi) {
                if (!empty($oi['product']['foto'])) {
                    $data['order_items'][$i]['product']['foto_url'] = url('storage/' . $oi['product']['foto']);
                }
            }
        }
        return $data;
    }
}
