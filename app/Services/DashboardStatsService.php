<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Statistik dashboard untuk admin (pedagang, pembeli, operator, grafik bulanan).
     */
    public function getStatsForAdmin(): array
    {
        $users = User::whereIn('role', ['admin', 'operator'])->paginate(10);
        $totalPedagang = Seller::count();
        $totalPembeli = Buyer::count();
        $totalOperator = User::where('role', 'operator')->count();

        $chart = $this->getMonthlyChartData();
        $percentages = $this->getYearOverYearPercentages();

        return array_merge([
            'users' => $users,
            'totalPedagang' => $totalPedagang,
            'totalPembeli' => $totalPembeli,
            'totalOperator' => $totalOperator,
        ], $chart, $percentages);
    }

    /**
     * Statistik dashboard untuk operator (sama tanpa akses admin-only).
     */
    public function getStatsForOperator(): array
    {
        $users = User::whereIn('role', ['admin', 'operator'])->paginate(10);
        $totalPedagang = Seller::count();
        $totalPembeli = Buyer::count();
        $totalOperator = User::where('role', 'operator')->count();

        $chart = $this->getMonthlyChartData();
        $percentages = $this->getYearOverYearPercentages();

        return array_merge([
            'users' => $users,
            'totalPedagang' => $totalPedagang,
            'totalPembeli' => $totalPembeli,
            'totalOperator' => $totalOperator,
        ], $chart, $percentages);
    }

    /**
     * Data grafik per bulan (pedagang & pembeli) tahun berjalan.
     */
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

        $months = range(1, 12);
        $pedagangTotals = array_fill(0, 12, 0);
        $pembeliTotals = array_fill(0, 12, 0);

        foreach ($pedagangData as $data) {
            $pedagangTotals[$data->month - 1] = $data->total;
        }
        foreach ($pembeliData as $data) {
            $pembeliTotals[$data->month - 1] = $data->total;
        }

        return [
            'months' => $months,
            'pedagangTotals' => $pedagangTotals,
            'pembeliTotals' => $pembeliTotals,
        ];
    }

    /**
     * Persentase perubahan YoY untuk pedagang, pembeli, operator.
     */
    protected function getYearOverYearPercentages(): array
    {
        $totalPedagangThisYear = Seller::whereYear('created_at', now()->year)->count();
        $totalPedagangLastYear = Seller::whereYear('created_at', now()->subYear()->year)->count();
        $totalPembeliThisYear = Buyer::whereYear('created_at', now()->year)->count();
        $totalPembeliLastYear = Buyer::whereYear('created_at', now()->subYear()->year)->count();
        $totalOperatorThisYear = User::whereYear('created_at', now()->year)->where('role', 'operator')->count();
        $totalOperatorLastYear = User::whereYear('created_at', now()->subYear()->year)->where('role', 'operator')->count();

        $pedagangPercentageChange = $totalPedagangLastYear > 0
            ? (($totalPedagangThisYear - $totalPedagangLastYear) / $totalPedagangLastYear) * 100 : 0;
        $pembeliPercentageChange = $totalPembeliLastYear > 0
            ? (($totalPembeliThisYear - $totalPembeliLastYear) / $totalPembeliLastYear) * 100 : 0;
        $operatorPercentageChange = $totalOperatorLastYear > 0
            ? (($totalOperatorThisYear - $totalOperatorLastYear) / $totalOperatorLastYear) * 100 : 0;

        $pedagangPercentageChange = round($pedagangPercentageChange, 2);
        $pembeliPercentageChange = round($pembeliPercentageChange, 2);
        $operatorPercentageChange = round($operatorPercentageChange, 2);

        return [
            'pedagangPercentageText' => $pedagangPercentageChange > 0 ? "+{$pedagangPercentageChange}%" : "{$pedagangPercentageChange}%",
            'pembeliPercentageText' => $pembeliPercentageChange > 0 ? "+{$pembeliPercentageChange}%" : "{$pembeliPercentageChange}%",
            'operatorPercentageText' => $operatorPercentageChange > 0 ? "+{$operatorPercentageChange}%" : "{$operatorPercentageChange}%",
            'totalOperatorThisYear' => $totalOperatorThisYear,
        ];
    }
}
