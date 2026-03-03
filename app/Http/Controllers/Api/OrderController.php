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
