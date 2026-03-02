<?php

namespace App\Services\Web;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthWebService
{
    /**
     * Validate credentials for web panel and ensure user has admin/operator role.
     *
     * @throws \RuntimeException on invalid credentials or forbidden role.
     */
    public function validateCredentials(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new \RuntimeException('Invalid email or password');
        }

        if (! $user->hasAnyRole(['admin', 'operator'])) {
            throw new \RuntimeException('This account cannot access the web panel.');
        }

        return $user;
    }

    /**
     * Determine dashboard route name based on user role.
     */
    public function dashboardRouteFor(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin.dashboard';
        }

        return 'operator.dashboard';
    }
}

