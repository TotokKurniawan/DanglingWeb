<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\Api\OrderService;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected OrderService $orderService,
    ) {}

    public function getOrderHistory(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $roleContext = $request->query('role'); // 'buyer' or 'seller'

        if ($roleContext === 'seller' && $user->seller) {
            $orders = $this->orderService->getHistoryForSeller($user);
            return $this->success([
                'role' => 'pedagang',
                'orders' => $this->formatOrdersForResponse($orders),
            ], 'Success', 200);
        }

        if ($roleContext === 'buyer' && $user->buyer) {
            $orders = $this->orderService->getHistoryForBuyer($user);
            return $this->success([
                'role' => 'pembeli',
                'orders' => $this->formatOrdersForResponse($orders),
            ], 'Success', 200);
        }

        // Fallback for missing context
        if ($user->buyer) {
            $orders = $this->orderService->getHistoryForBuyer($user);
            return $this->success([
                'role' => 'pembeli',
                'orders' => $this->formatOrdersForResponse($orders),
            ], 'Success', 200);
        }

        if ($user->seller) {
            $orders = $this->orderService->getHistoryForSeller($user);
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
                if (!empty($oi['product']['photo_path'])) {
                    $arr['order_items'][$i]['product']['photo_url'] = url('storage/' . $oi['product']['photo_path']);
                }
            }
            return $arr;
        })->all();
    }
}
