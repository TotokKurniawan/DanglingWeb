<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        // Jika pengguna sudah login, redirect ke halaman yang sesuai berdasarkan peran
        if (Auth::check()) {
            $user = Auth::user();
            return redirect()->route($user->role === 'admin' ? 'Dashboard' : 'DashboardOperator');
        }

        // Jika pengguna belum login, tampilkan halaman login
        return view('admin.login.Login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Ambil hanya email dan password
        $credentials = $request->only('email', 'password');

        // Cari pengguna berdasarkan email
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        // Periksa apakah pengguna ada dan password cocok
        if ($user && $user->password === $credentials['password']) {
            // Jika login berhasil, autentikasi pengguna
            Auth::login($user);

            // Simpan email ke session
            session(['user_email' => $user->email]);

            // Tambahkan pesan flash untuk SweetAlert
            session()->flash('success', 'Login berhasil! Selamat datang, ' . $user->nama);

            // Redirect ke halaman berdasarkan role user
            return redirect()->route($user->role === 'admin' ? 'Dashboard' : 'DashboardOperator');
        }

        // Jika login gagal
        return back()->withErrors(['email' => 'Email atau password salah']);
    }


    public function logout(Request $request)
    {
        Auth::logout(); // Melakukan logout
        return redirect()->route('Login'); // Ganti 'login' dengan route yang sesuai jika perlu
    }
}
