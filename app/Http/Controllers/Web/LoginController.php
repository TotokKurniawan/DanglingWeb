<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\LoginRequest;
use App\Services\Web\AuthWebService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        protected AuthWebService $authWebService,
    ) {}

    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $route = $this->authWebService->dashboardRouteFor($user);
            return redirect()->route($route);
        }
        return view('admin.login.Login');
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->authWebService->validateCredentials(
                $request->input('email'),
                $request->input('password'),
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }

        Auth::login($user);
        session(['user_email' => $user->email]);
        session()->flash('success', 'Login successful. Welcome, ' . ($user->name ?? $user->email));

        $route = $this->authWebService->dashboardRouteFor($user);
        return redirect()->route($route);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login.show');
    }
}
