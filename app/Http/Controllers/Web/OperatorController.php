<?php

namespace App\Http\Controllers\Web;

use App\Models\Complaint;
use App\Models\Seller;
use App\Models\User;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function dashboard(DashboardStatsService $statsService)
    {
        $stats = $statsService->getStatsForOperator();
        return view('operator.dashboard', $stats);
    }

    public function indexSellers()
    {
        $pedagangs = Seller::paginate(10);
        return view('operator.pedagang', compact('pedagangs'));
    }

    public function indexComplaints()
    {
        $keluhans = Complaint::with(['buyer', 'seller'])->paginate(10);
        return view('operator.keluhan', compact('keluhans'));
    }
}
