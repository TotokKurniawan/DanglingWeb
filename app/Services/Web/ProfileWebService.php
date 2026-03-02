<?php

namespace App\Services\Web;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileWebService
{
    public function updateProfile(User $user, array $data, ?string $photoPath = null): User
    {
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        if ($photoPath !== null) {
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $user->photo_path = $photoPath;
        }

        $user->save();

        return $user;
    }
}

