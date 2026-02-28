<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class ForgotController extends Controller
{
    public function showForgotForm(Request $request)
    {
        return view('admin.login.Lupa');
    }
}

