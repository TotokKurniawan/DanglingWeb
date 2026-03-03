<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use App\Services\Api\OrderService;
use App\Services\FcmNotificationService;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected OrderService $orderService,
        protected FcmNotificationService $fcm,
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
        if ($authUser && $order) {
            $this->authorize('manageAsSeller', $order);
        }
        try {
            $order = $this->orderService->accept($order, $authUser);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        // Notify buyer: order diterima
        $this->notifyBuyer($order, 'accepted');

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
        if ($authUser && $order) {
            $this->authorize('manageAsSeller', $order);
        }
        try {
            $order = $this->orderService->reject($order, $authUser, $request->input('reason'));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        // Notify buyer: order ditolak
        $this->notifyBuyer($order, 'rejected');

        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function completeOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        if ($authUser && $order) {
            $this->authorize('manageAsSeller', $order);
        }
        try {
            $order = $this->orderService->complete($order, $authUser);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        // Notify buyer: order selesai
        $this->notifyBuyer($order, 'completed');

        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        if ($authUser && $order) {
            $this->authorize('manageAsSeller', $order);
        }
        try {
            $order = $this->orderService->cancelBySeller($order, $authUser, $request->input('reason'));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        // Notify buyer: order dibatalkan seller
        $this->notifyBuyer($order, 'cancelled');

        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    /**
     * Pembeli membatalkan order (hanya status Menunggu).
     */
    public function cancelByBuyer(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        if ($authUser && $order) {
            $this->authorize('cancelAsBuyer', $order);
        }
        try {
            $order = $this->orderService->cancelByBuyer($order, $authUser, $request->input('reason'));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        // Notify seller: order dibatalkan buyer
        $this->notifySeller($order);

        return $this->success(['order' => $order], 'Order dibatalkan', 200);
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    /**
     * Kirim push notification ke buyer tentang perubahan status order.
     */
    private function notifyBuyer(Order $order, string $status): void
    {
        try {
            $order->loadMissing('buyer');
            if ($order->buyer && $order->buyer->user_id) {
                $this->fcm->notifyOrderStatusChanged($order->buyer->user_id, $order->id, $status);
            }
        } catch (\Throwable $e) {
            // Jangan gagalkan request utama jika push gagal
            \Log::warning('Push to buyer failed: ' . $e->getMessage());
        }
    }

    /**
     * Kirim push notification ke seller bahwa buyer cancel.
     */
    private function notifySeller(Order $order): void
    {
        try {
            $order->loadMissing(['seller', 'buyer']);
            if ($order->seller && $order->seller->user_id) {
                $buyerName = $order->buyer->name ?? 'Pembeli';
                $this->fcm->notifyNewOrderForSeller($order->seller->user_id, $order->id, $buyerName);
            }
        } catch (\Throwable $e) {
            \Log::warning('Push to seller failed: ' . $e->getMessage());
        }
    }
}
