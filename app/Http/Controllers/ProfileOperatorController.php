<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileOperatorController extends Controller
{
    public function ProfileOperator(Request $request)
    {
        // Mengambil email dari session
        $email = session('user_email');

        // Mengambil pengguna berdasarkan email
        $user = \App\Models\User::where('email', $email)->first();

        // Mengembalikan tampilan dengan data pengguna
        return view('operator.profile', compact('user'));
    }
}
