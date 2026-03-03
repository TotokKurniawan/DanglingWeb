<?php

namespace App\Http\Controllers\Web;

use App\Models\Complaint;
use App\Models\Seller;
use App\Models\User;
use App\Services\Web\DashboardStatsService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(DashboardStatsService $statsService)
    {
        $stats = $statsService->getStatsForAdmin();
        return view('admin.dashboard', $stats);
    }

    public function indexSellers(Request $request)
    {
        $query = Seller::query();

        if ($request->filled('sort')) {
            match ($request->sort) {
                'rating'     => $query->orderByDesc('rating_average'),
                'complaints' => $query->withCount('complaints')->orderByDesc('complaints_count'),
                default      => $query->orderBy('store_name'),
            };
        } else {
            $query->orderBy('store_name');
        }

        $pedagangs = $query->paginate(10)->appends($request->query());
        return view('admin.pedagang', compact('pedagangs'));
    }

    public function indexOperators()
    {
        $users = User::role('admin')->paginate(10);
        return view('admin.dataadmin', compact('users'));
    }

    public function createOperatorForm()
    {
        return view('admin.form.tambahadmin');
    }

    /**
     * Daftar keluhan — dengan filter seller, status, tanggal.
     */
    public function indexComplaints(Request $request)
    {
        $query = Complaint::with(['buyer', 'seller', 'order']);

        // Filter by seller
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $keluhans = $query->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        $sellers = Seller::orderBy('store_name')->get(['id', 'store_name']);

        return view('admin.keluhan', compact('keluhans', 'sellers'));
    }

    /**
     * PATCH /admin/complaints/{id}/status — update status keluhan.
     */
    public function updateComplaintStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,dismissed',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->handled_by = auth()->id();
        $complaint->handled_at = now();
        $complaint->save();

        return redirect()->back()->with('success', 'Status keluhan berhasil diperbarui.');
    }

    public function showProfile()
    {
        $user = User::role('admin')->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Admin user not found.');
        }
        return view('admin.profile', compact('user'));
    }
}
