<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RejectOrderRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    use ApiResponse;

    public function getPendingOrders(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser || !$authUser->seller) {
            return $this->error('Forbidden', 403);
        }

        $orders = Order::with(['orderItems.product', 'buyer'])
            ->where('id_pedagang', $authUser->seller->id)
            ->where('status', Order::STATUS_PENDING)
            ->get();

        return $this->success(['orders' => $orders], 'Success', 200);
    }

    public function acceptOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        if (!$authUser || !$authUser->seller || (int) $order->id_pedagang !== (int) $authUser->seller->id) {
            return $this->error('Forbidden', 403);
        }

        if (!$order->canBeAccepted()) {
            return $this->error('Order hanya bisa diterima jika status Menunggu', 422);
        }

        $order->status = Order::STATUS_ACCEPTED;
        $order->save();
        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function rejectOrder(Request $request, $id)
    {
        $request->validate([
            'alasan_tolak' => 'required|string|max:255',
        ]);

        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        if (!$authUser || !$authUser->seller || (int) $order->id_pedagang !== (int) $authUser->seller->id) {
            return $this->error('Forbidden', 403);
        }

        if (!$order->canBeAccepted()) {
            return $this->error('Order hanya bisa ditolak jika status Menunggu', 422);
        }

        $order->status = Order::STATUS_REJECTED;
        $order->alasan_tolak = $request->validated()['alasan_tolak'];
        $order->save();
        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function completeOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        if (!$authUser || !$authUser->seller || (int) $order->id_pedagang !== (int) $authUser->seller->id) {
            return $this->error('Forbidden', 403);
        }

        if (!$order->isAccepted()) {
            return $this->error('Order hanya bisa diselesaikan jika status Diterima', 422);
        }

        $order->status = Order::STATUS_COMPLETED;
        $order->save();
        return $this->success(['order' => $order], 'Status updated successfully', 200);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }

        $authUser = $request->user();
        if (!$authUser || !$authUser->seller || (int) $order->id_pedagang !== (int) $authUser->seller->id) {
            return $this->error('Forbidden', 403);
        }

        if (!$order->canBeCancelledBySeller()) {
            return $this->error('Order hanya bisa dibatalkan seller jika status Menunggu atau Diterima', 422);
        }

        $order->status = Order::STATUS_CANCELLED;
        $order->save();
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
        if (!$authUser || !$authUser->buyer || (int) $order->id_pembeli !== (int) $authUser->buyer->id) {
            return $this->error('Forbidden', 403);
        }

        if (!$order->canBeCancelledByBuyer()) {
            return $this->error('Order hanya bisa dibatalkan pembeli jika status Menunggu', 422);
        }

        $order->status = Order::STATUS_CANCELLED;
        $order->save();
        return $this->success(['order' => $order], 'Order dibatalkan', 200);
    }
}
