<?php

/**
 * @deprecated MVP
 *
 * Controller ini DITANGGUHKAN sementara di fase MVP.
 * Semua fungsi operator (dashboard, daftar seller, daftar keluhan)
 * kini dipegang langsung oleh AdminController dengan role `admin`.
 *
 * Rencana reaktivasi:
 * - Aktifkan kembali jika jumlah user internal bertambah dan perlu
 *   pemisahan akses antara admin penuh vs operator terbatas.
 * - Daftar izin yang akan dipindahkan ke operator saat reaktivasi:
 *   * view_sellers
 *   * view_complaints
 *   * view_operator_dashboard
 *
 * @see AdminController untuk implementasi aktif yang setara.
 */

namespace App\Http\Controllers\Web;

use App\Models\Complaint;
use App\Models\Seller;
use App\Services\Web\DashboardStatsService;

class OperatorController extends Controller
{
    /** @deprecated Gunakan AdminController::dashboard() */
    public function dashboard(DashboardStatsService $statsService)
    {
        $stats = $statsService->getStatsForOperator();
        return view('operator.dashboard', $stats);
    }

    /** @deprecated Gunakan AdminController::indexSellers() */
    public function indexSellers()
    {
        $pedagangs = Seller::paginate(10);
        return view('operator.pedagang', compact('pedagangs'));
    }

    /** @deprecated Gunakan AdminController::indexComplaints() */
    public function indexComplaints()
    {
        $keluhans = Complaint::with(['buyer', 'seller'])->paginate(10);
        return view('operator.keluhan', compact('keluhans'));
    }
}
