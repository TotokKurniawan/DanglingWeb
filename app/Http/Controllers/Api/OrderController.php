<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateOrderRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use App\Services\Api\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected OrderService $orderService,
    ) {}

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"seller_id","payment_method","items"},
     *             @OA\Property(property="seller_id", type="integer", example=1),
     *             @OA\Property(property="payment_method", type="string", example="TRANSFER"),
     *             @OA\Property(property="notes", type="string", example="Tolong jangan pedas"),
     *             @OA\Property(property="voucher_code", type="string", example="PROMO_2026"),
     *             @OA\Property(
     *                 property="items", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createOrder(CreateOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder(
                $request->user(),
                $request->validated()
            );
        } catch (\Throwable $e) {
            if ($e instanceof \RuntimeException) {
                return $this->error($e->getMessage(), 422);
            }
            return $this->error('Failed to create order', 500);
        }

        return $this->success(['order' => $this->formatOrder($order)], 'Order created successfully', 201);
    }

    /**
     * GET /api/orders/{id} — detail satu order.
     */
    public function show(Request $request, $id)
    {
        $order = Order::with(['orderItems.product', 'buyer', 'seller'])->find($id);
        if (! $order) {
            return $this->error('Order not found', 404);
        }

        $user = $request->user();
        $buyer = $user->buyer;
        $seller = $user->seller;

        // Pastikan order milik user ini (sebagai buyer atau seller)
        $isBuyer  = $buyer && $order->buyer_id === $buyer->id;
        $isSeller = $seller && $order->seller_id === $seller->id;

        if (! $isBuyer && ! $isSeller) {
            return $this->error('Forbidden', 403);
        }

        return $this->success(['order' => $this->formatOrder($order)], 'Order detail', 200);
    }

    /**
     * POST /api/orders/{id}/reorder — buat order baru dari order lama.
     */
    public function reorder(Request $request, $id)
    {
        $order = Order::find($id);
        if (! $order) {
            return $this->error('Order not found', 404);
        }

        try {
            $newOrder = $this->orderService->reorder($order, $request->user());
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['order' => $this->formatOrder($newOrder)], 'Re-order berhasil', 201);
    }

    /**
     * PUT /api/orders/{id}/confirm-payment — upload bukti pembayaran.
     */
    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $order = Order::find($id);
        if (! $order) {
            return $this->error('Order not found', 404);
        }

        $user = $request->user();
        $buyer = $user->buyer;
        if (! $buyer || $order->buyer_id !== $buyer->id) {
            return $this->error('Forbidden', 403);
        }

        if ($order->payment_method !== Order::PAYMENT_TRANSFER) {
            return $this->error('Konfirmasi pembayaran hanya untuk metode transfer.', 422);
        }

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return $this->error('Pembayaran sudah dikonfirmasi sebelumnya.', 422);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof_path' => $path,
            'payment_status'     => Order::PAYMENT_PAID,
        ]);

        return $this->success([
            'order' => $this->formatOrder($order->fresh(['orderItems.product', 'buyer', 'seller'])),
        ], 'Bukti pembayaran berhasil diupload.', 200);
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
                if (!empty($oi['product']['photo_path'])) {
                    $data['order_items'][$i]['product']['photo_url'] = url('storage/' . $oi['product']['photo_path']);
                }
            }
        }
        return $data;
    }
}
