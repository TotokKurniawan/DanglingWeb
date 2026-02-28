<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileOperatorController extends Controller
{
    public function showProfile(Request $request)
    {
        $email = session('user_email');
        $user = User::where('email', $email)->first();
        return view('operator.profile', compact('user'));
    }
}
