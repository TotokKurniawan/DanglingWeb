<?php

namespace App\Http\Controllers;

use App\Models\Keluhan;
use App\Models\Pedagang;
use App\Models\Pembeli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::whereIn('role', ['admin', 'operator'])->paginate(10);
        $totalPedagang = Pedagang::count();
        $totalPembeli = Pembeli::count();
        $totalOperator = User::whereIn('role', ['operator'])->count();

        // Mengambil data pedagang berdasarkan bulan untuk tahun ini
        $pedagangData = Pedagang::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', now()) // Hanya data tahun ini
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        // Mengambil data pembeli berdasarkan bulan untuk tahun ini
        $pembeliData = Pembeli::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', now()) // Hanya data tahun ini
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        // Transformasi data untuk view
        $months = range(1, 12); // Januari - Desember
        $pedagangTotals = array_fill(0, 12, 0); // Default 0 untuk setiap bulan
        $pembeliTotals = array_fill(0, 12, 0); // Default 0 untuk setiap bulan

        foreach ($pedagangData as $data) {
            $pedagangTotals[$data->month - 1] = $data->total;
        }
        foreach ($pembeliData as $data) {
            $pembeliTotals[$data->month - 1] = $data->total;
        }

        // Menghitung total pedagang dan pembeli tahun ini dan tahun lalu
        $totalPedagangThisYear = Pedagang::whereYear('created_at', now())->count();
        $totalPedagangLastYear = Pedagang::whereYear('created_at', now()->subYear())->count();

        $totalPembeliThisYear = Pembeli::whereYear('created_at', now())->count();
        $totalPembeliLastYear = Pembeli::whereYear('created_at', now()->subYear())->count();

        $totalOperatorThisYear = User::whereYear('created_at', now())->whereIn('role', ['operator'])->count(); // Operator tahun ini
        $totalOperatorLastYear = User::whereYear('created_at', now()->subYear())->whereIn('role', ['operator'])->count(); // Operator tahun lalu

        // Menghitung persentase perubahan untuk pedagang, pembeli, dan operator
        $pedagangPercentageChange = 0;
        if ($totalPedagangLastYear > 0) {
            $pedagangPercentageChange = (($totalPedagangThisYear - $totalPedagangLastYear) / $totalPedagangLastYear) * 100;
        }

        $pembeliPercentageChange = 0;
        if ($totalPembeliLastYear > 0) {
            $pembeliPercentageChange = (($totalPembeliThisYear - $totalPembeliLastYear) / $totalPembeliLastYear) * 100;
        }

        $operatorPercentageChange = 0;
        if ($totalOperatorLastYear > 0) {
            $operatorPercentageChange = (($totalOperatorThisYear - $totalOperatorLastYear) / $totalOperatorLastYear) * 100;
        }

        // Jika persentase perubahan positif, tambahkan tanda "+" di depan
        $pedagangPercentageText = $pedagangPercentageChange > 0 ? "+{$pedagangPercentageChange}%" : "{$pedagangPercentageChange}%";
        $pembeliPercentageText = $pembeliPercentageChange > 0 ? "+{$pembeliPercentageChange}%" : "{$pembeliPercentageChange}%";
        $operatorPercentageText = $operatorPercentageChange > 0 ? "+{$operatorPercentageChange}%" : "{$operatorPercentageChange}%";

        return view('admin.dashboard', compact(
            'users',
            'totalPedagang',
            'totalPembeli',
            'totalOperator',
            'months',
            'pedagangTotals',
            'pembeliTotals',
            'pedagangPercentageText',
            'pembeliPercentageText',
            'operatorPercentageText',
            'totalOperatorThisYear'
        ));
    }



    public function pedagang()
    {

        $pedagangs = Pedagang::paginate(10);
        return view('admin.pedagang', compact('pedagangs'));
    }
    public function dataadmin()
    {
        $users = User::whereIn('role', ['operator'])->paginate(10);
        return view('admin.dataadmin', compact(var_name: 'users'));
    }
    public function tambahadmin()
    {
        return view('admin.form.tambahadmin');
    }
    public function keluhan()
    {
        $keluhans = Keluhan::with(['pembeli', 'pedagang'])->paginate(10);
        return view('admin.keluhan', compact('keluhans'));
    }

    public function profile()
    {
        // Ambil data pengguna dengan role 'admin'
        $user = User::where('role', 'admin')->first(); // Mengambil pengguna dengan role admin

        // Cek jika pengguna ditemukan
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna dengan role admin tidak ditemukan.');
        }

        return view('admin.profile', compact('user'));
    }

    // public function profile()
    // {
    //     // Ambil email dari sesi
    //     $email = Session::get('email');

    //     // Ambil data pengguna berdasarkan email dan role admin
    //     $user = User::where('email', $email)->where('role', 'admin')->first();

    //     // Cek jika pengguna ditemukan
    //     if (!$user) {
    //         return redirect()->back()->with('error', 'Pengguna tidak ditemukan atau bukan admin.');
    //     }

    //     return view('admin.profile', compact('user'));
    // }

}
