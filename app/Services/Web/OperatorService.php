<?php

namespace App\Services\Web;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class OperatorService
{
    public function createOperator(array $data, ?string $photoPath = null): User
    {
        $user = new User();
        $user->name = $data['name'] ?? '';
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);

        if ($photoPath) {
            $user->photo_path = $photoPath;
        }

        $user->save();

        // Assign Spatie role for web guard (saat ini hanya admin untuk web)
        $user->assignRole('admin');

        return $user;
    }

    public function updateOperator(User $user, array $data): User
    {
        $user->name = $data['name'] ?? $user->name;

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Untuk sementara, semua user internal dianggap admin.

        $user->save();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $user->delete();
    }
}

