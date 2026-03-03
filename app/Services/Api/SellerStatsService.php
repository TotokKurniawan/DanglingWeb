<?php

namespace App\Services\Api;

use App\Models\Complaint;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SellerStatsService
{
    /**
     * Ringkasan pendapatan & performa seller.
     */
    public function getStats(User $sellerUser): array
    {
        $seller = $sellerUser->seller;
        if (! $seller) {
            throw new \RuntimeException('Seller profile not found');
        }

        $sellerId = $seller->id;

        // Total order per status
        $orderCounts = Order::where('seller_id', $sellerId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalOrders    = array_sum($orderCounts);
        $completedCount = $orderCounts[Order::STATUS_COMPLETED] ?? 0;

        // Pendapatan total (dari order completed)
        $totalRevenue = Order::where('seller_id', $sellerId)
            ->where('status', Order::STATUS_COMPLETED)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

        // Pendapatan hari ini
        $todayRevenue = Order::where('seller_id', $sellerId)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereDate('orders.completed_at', today())
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

        // Pendapatan minggu ini
        $weekRevenue = Order::where('seller_id', $sellerId)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereBetween('orders.completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

        // Jumlah komplain
        $complaintCount = Complaint::where('seller_id', $sellerId)->count();

        return [
            'total_orders'      => $totalOrders,
            'completed_orders'  => $completedCount,
            'order_breakdown'   => $orderCounts,
            'total_revenue'     => (float) $totalRevenue,
            'today_revenue'     => (float) $todayRevenue,
            'week_revenue'      => (float) $weekRevenue,
            'rating_average'    => $seller->rating_average,
            'rating_count'      => $seller->rating_count,
            'complaint_count'   => $complaintCount,
        ];
    }
}
