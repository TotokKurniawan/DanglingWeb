<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/forgot-password
     * Kirim link reset password ke email user.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success([], 'Link reset password telah dikirim ke email Anda.', 200);
        }

        return $this->error('Gagal mengirim link reset. Coba lagi nanti.', 422);
    }

    /**
     * POST /api/reset-password
     * Reset password menggunakan token dari email.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success([], 'Password berhasil direset. Silakan login.', 200);
        }

        return $this->error('Token tidak valid atau sudah expired.', 422);
    }

    /**
     * PUT /api/change-password
     * Ganti password (harus login, kirim old_password + new_password).
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();
        if (! $user) {
            return $this->error('Unauthenticated', 401);
        }

        if (! Hash::check($request->old_password, $user->password)) {
            return $this->error('Password lama salah.', 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return $this->success([], 'Password berhasil diubah.', 200);
    }
}
