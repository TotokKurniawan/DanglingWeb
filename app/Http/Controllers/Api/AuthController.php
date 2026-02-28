<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Buyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        $token = $user->createToken('token')->accessToken;
        return $this->success([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ], 'Login successful', 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
        ]);

        Buyer::create([
            'user_id' => $user->id,
            'nama' => '',
            'telfon' => '',
            'alamat' => '',
            'foto' => '',
        ]);

        return $this->success([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 'Registration successful', 201);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $token = $user->token();
        if ($token) {
            $token->revoke();
        }

        return $this->success(null, 'Successfully logged out', 200);
    }
}
