<?php

namespace App\Http\Controllers\Web;

use App\Models\Complaint;
use App\Models\Seller;
use App\Models\User;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(DashboardStatsService $statsService)
    {
        $stats = $statsService->getStatsForAdmin();
        return view('admin.dashboard', $stats);
    }

    public function indexSellers()
    {
        $pedagangs = Seller::paginate(10);
        return view('admin.pedagang', compact('pedagangs'));
    }

    public function indexOperators()
    {
        $users = User::whereIn('role', ['operator'])->paginate(10);
        return view('admin.dataadmin', compact('users'));
    }

    public function createOperatorForm()
    {
        return view('admin.form.tambahadmin');
    }

    public function indexComplaints()
    {
        $keluhans = Complaint::with(['buyer', 'seller'])->paginate(10);
        return view('admin.keluhan', compact('keluhans'));
    }

    public function showProfile()
    {
        $user = User::where('role', 'admin')->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Admin user not found.');
        }
        return view('admin.profile', compact('user'));
    }
}
