<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    /**
     * Admin dapat melihat semua komplain.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Admin dapat melihat detail satu komplain.
     */
    public function view(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('admin');
    }
}

