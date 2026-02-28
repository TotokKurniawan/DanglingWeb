<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard');
        }
        return view('admin.login.Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if (!in_array($user->role, ['admin', 'operator'], true)) {
                return back()->withErrors(['email' => 'This account cannot access the web panel.']);
            }

            Auth::login($user);
            session(['user_email' => $user->email]);
            session()->flash('success', 'Login successful. Welcome, ' . ($user->nama ?? $user->email));
            return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login.show');
    }
}
