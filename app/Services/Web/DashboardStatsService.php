<?php

namespace App\Services\Web;

use App\Models\Buyer;
use App\Models\Complaint;
use App\Models\Order;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Statistik dashboard untuk admin.
     */
    public function getStatsForAdmin(): array
    {
        $users = User::role('admin')->paginate(10);
        $totalPedagang = Seller::count();
        $totalPembeli = Buyer::count();
        $totalOperator = User::role('admin')->count();

        $chart = $this->getMonthlyChartData();
        $percentages = $this->getYearOverYearPercentages();

        // ── Widget tambahan Modul 7 ──
        $orderStats = $this->getOrderStats();
        $topSellers = $this->getTopSellers();
        $complaintSummary = $this->getComplaintSummary();

        return array_merge([
            'users' => $users,
            'totalPedagang' => $totalPedagang,
            'totalPembeli' => $totalPembeli,
            'totalOperator' => $totalOperator,
        ], $chart, $percentages, $orderStats, [
            'topSellers' => $topSellers,
            'complaintSummary' => $complaintSummary,
        ]);
    }

    /**
     * @deprecated Gunakan getStatsForAdmin(). Operator role ditangguhkan di MVP.
     */
    public function getStatsForOperator(): array
    {
        return $this->getStatsForAdmin();
    }

    // ─── Order stats ────────────────────────────────────────────────────────

    protected function getOrderStats(): array
    {
        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();

        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersThisWeek = Order::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        $completedToday = Order::where('status', Order::STATUS_COMPLETED)
            ->whereDate('completed_at', $today)->count();

        return [
            'ordersToday' => $ordersToday,
            'ordersThisWeek' => $ordersThisWeek,
            'completedToday' => $completedToday,
        ];
    }

    // ─── Top sellers ────────────────────────────────────────────────────────

    protected function getTopSellers(int $limit = 5): array
    {
        return Seller::select('sellers.*')
            ->selectRaw('(SELECT COUNT(*) FROM orders WHERE orders.seller_id = sellers.id AND orders.status = ?) as order_count', [Order::STATUS_COMPLETED])
            ->orderByDesc('order_count')
            ->orderByDesc('rating_average')
            ->limit($limit)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'store_name' => $s->store_name,
                'rating_average' => $s->rating_average,
                'rating_count' => $s->rating_count,
                'order_count' => $s->order_count,
            ])
            ->toArray();
    }

    // ─── Complaint summary ──────────────────────────────────────────────────

    protected function getComplaintSummary(): array
    {
        $counts = Complaint::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'open' => $counts[Complaint::STATUS_OPEN] ?? 0,
            'in_progress' => $counts[Complaint::STATUS_IN_PROGRESS] ?? 0,
            'resolved' => $counts[Complaint::STATUS_RESOLVED] ?? 0,
            'dismissed' => $counts[Complaint::STATUS_DISMISSED] ?? 0,
            'total' => array_sum($counts),
        ];
    }

    // ─── Chart bulanan (existing) ───────────────────────────────────────────

    protected function getMonthlyChartData(): array
    {
        $pedagangData = Seller::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        $pembeliData = Buyer::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        $pedagangTotals = array_fill(0, 12, 0);
        $pembeliTotals = array_fill(0, 12, 0);

        foreach ($pedagangData as $data) {
            $pedagangTotals[$data->month - 1] = $data->total;
        }
        foreach ($pembeliData as $data) {
            $pembeliTotals[$data->month - 1] = $data->total;
        }

        return [
            'months' => range(1, 12),
            'pedagangTotals' => $pedagangTotals,
            'pembeliTotals' => $pembeliTotals,
        ];
    }

    // ─── YoY percentages (existing) ─────────────────────────────────────────

    protected function getYearOverYearPercentages(): array
    {
        $totalPedagangThisYear = Seller::whereYear('created_at', now()->year)->count();
        $totalPedagangLastYear = Seller::whereYear('created_at', now()->subYear()->year)->count();
        $totalPembeliThisYear = Buyer::whereYear('created_at', now()->year)->count();
        $totalPembeliLastYear = Buyer::whereYear('created_at', now()->subYear()->year)->count();
        $totalOperatorThisYear = User::role('admin')->whereYear('created_at', now()->year)->count();
        $totalOperatorLastYear = User::role('admin')->whereYear('created_at', now()->subYear()->year)->count();

        $calc = fn ($cur, $prev) => $prev > 0 ? round((($cur - $prev) / $prev) * 100, 2) : 0;
        $format = fn ($v) => $v > 0 ? "+{$v}%" : "{$v}%";

        $p = $calc($totalPedagangThisYear, $totalPedagangLastYear);
        $b = $calc($totalPembeliThisYear, $totalPembeliLastYear);
        $o = $calc($totalOperatorThisYear, $totalOperatorLastYear);

        return [
            'pedagangPercentageText' => $format($p),
            'pembeliPercentageText' => $format($b),
            'operatorPercentageText' => $format($o),
            'totalOperatorThisYear' => $totalOperatorThisYear,
        ];
    }
}
