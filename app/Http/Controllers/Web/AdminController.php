<?php

namespace App\Http\Controllers\Web;

use App\Models\ActivityLog;
use App\Models\Buyer;
use App\Models\Complaint;
use App\Models\Order;
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

    /**
     * Download CSV data pedagang.
     */
    public function exportSellers(Request $request)
    {
        $sellers = Seller::with('user')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=sellers_export_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nama Toko', 'Email', 'Telepon', 'Status Online', 'Status Suspend', 'Rating', 'Jumlah Orders', 'Tanggal Gabung'];

        $callback = function () use ($sellers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($sellers as $seller) {
                $row = [
                    $seller->id,
                    $seller->store_name,
                    $seller->user->email ?? '-',
                    $seller->phone,
                    $seller->is_online ? 'Online' : 'Offline',
                    $seller->is_suspended ? 'Suspended' : 'Active',
                    $seller->rating_average ?? 0,
                    $seller->orders()->count(),
                    $seller->created_at->format('Y-m-d H:i:s'),
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function indexOperators()
    {
        $users = User::role('admin')->paginate(10);
        return view('admin.dataadmin', compact('users'));
    }

    public function indexBuyers(Request $request)
    {
        $query = Buyer::with('user');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $buyers = $query->paginate(10)->appends($request->query());
        return view('admin.buyers', compact('buyers'));
    }

    public function indexOrders(Request $request)
    {
        $query = Order::with(['buyer.user', 'seller', 'orderItems.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        $orders = $query->latest('created_at')->paginate(10)->appends($request->query());
        $sellers = Seller::orderBy('store_name')->get(['id', 'store_name']);

        return view('admin.orders', compact('orders', 'sellers'));
    }

    public function indexActivityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('event')) {
            $query->where('event', 'like', '%' . $request->event . '%');
        }

        $logs = $query->latest('created_at')->paginate(15)->appends($request->query());
        
        return view('admin.activity_logs', compact('logs'));
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
     * Download CSV data keluhan.
     */
    public function exportComplaints(Request $request)
    {
        $complaints = Complaint::with(['buyer.user', 'seller', 'order'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=complaints_export_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Order ID', 'Pembeli', 'Penjual', 'Tipe', 'Deskripsi', 'Status', 'Tanggal Keluhan'];

        $callback = function () use ($complaints, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($complaints as $c) {
                $row = [
                    $c->id,
                    $c->order->id ?? '-',
                    $c->buyer->user->name ?? '-',
                    $c->seller->store_name ?? '-',
                    $c->type,
                    $c->description,
                    $c->status,
                    $c->created_at->format('Y-m-d H:i:s'),
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
    /**
     * PATCH /admin/sellers/{id}/suspend — toggle suspend seller.
     */
    public function toggleSuspend(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);

        $isSuspending = ! $seller->is_suspended;

        $seller->update([
            'is_suspended'    => $isSuspending,
            'suspended_reason' => $isSuspending
                ? $request->input('reason', 'Disuspend oleh admin.')
                : null,
        ]);

        // Jika disuspend, otomatis set offline
        if ($isSuspending) {
            $seller->update(['is_online' => false]);
        }

        $message = $isSuspending ? 'Seller berhasil disuspend.' : 'Seller berhasil di-unsuspend.';
        return redirect()->back()->with('success', $message);
    }
}
