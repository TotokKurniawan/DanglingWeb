<?php

namespace App\Services\Web;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthWebService
{
    /**
     * Validate credentials for web panel and ensure user has admin role.
     *
     * @throws \RuntimeException on invalid credentials or forbidden role.
     */
    public function validateCredentials(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new \RuntimeException('Invalid email or password');
        }

        if (! $user->hasRole('admin')) {
            throw new \RuntimeException('This account cannot access the admin panel.');
        }

        return $user;
    }

    /**
     * Determine dashboard route name for web user.
     */
    public function dashboardRouteFor(User $user): string
    {
        // Saat ini hanya ada satu role internal (admin) untuk web panel.
        return 'admin.dashboard';
    }
}

