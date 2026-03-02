<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use App\Services\Api\OrderService;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected OrderService $orderService,
    ) {}

    public function getPendingOrders(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser || !$authUser->seller) {
            return $this->error('Forbidden', 403);
        }

        $orders = $this->orderService->getPendingForSeller($authUser);

        return $this->success(['orders' => $orders], 'Success', 200);
    }

    public function acceptOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        try {
            $order = $this->orderService->accept($order, $authUser);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }
        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function rejectOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        try {
            $order = $this->orderService->reject($order, $authUser, $request->input('reason'));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }
        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function completeOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        try {
            $order = $this->orderService->complete($order, $authUser);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }
        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        try {
            $order = $this->orderService->cancelBySeller($order, $authUser);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }
        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    /**
     * Pembeli membatalkan order (hanya status Menunggu).
     */
    public function cancelByBuyer(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        try {
            $order = $this->orderService->cancelByBuyer($order, $authUser);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }
        return $this->success(['order' => $order], 'Order dibatalkan', 200);
    }
}
