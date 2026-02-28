<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    use ApiResponse;

    public function getOrderHistory(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        if ($user->buyer) {
            $orders = Order::with(['orderItems.product', 'seller', 'buyer'])
                ->where('id_pembeli', $user->buyer->id)
                ->orderByDesc('created_at')
                ->get();
            return $this->success([
                'role' => 'pembeli',
                'orders' => $this->formatOrdersForResponse($orders),
            ], 'Success', 200);
        }

        if ($user->seller) {
            $orders = Order::with(['orderItems.product', 'seller', 'buyer'])
                ->where('id_pedagang', $user->seller->id)
                ->orderByDesc('created_at')
                ->get();
            return $this->success([
                'role' => 'pedagang',
                'orders' => $this->formatOrdersForResponse($orders),
            ], 'Success', 200);
        }

        return $this->error('Invalid user role', 403);
    }

    /**
     * Tambah foto_url pada product di order_items.
     */
    protected function formatOrdersForResponse($orders): array
    {
        return $orders->map(function ($order) {
            $arr = $order->toArray();
            foreach ($arr['order_items'] ?? [] as $i => $oi) {
                if (!empty($oi['product']['foto'])) {
                    $arr['order_items'][$i]['product']['foto_url'] = url('storage/' . $oi['product']['foto']);
                }
            }
            return $arr;
        })->all();
    }
}
