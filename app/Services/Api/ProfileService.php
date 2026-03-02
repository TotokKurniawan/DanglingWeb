<?php

namespace App\Services\Api;

use App\Models\Buyer;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updateBuyerProfile(User $user, Buyer $buyer, array $data, ?string $photoPath = null): Buyer
    {
        if ((int) $buyer->user_id !== (int) $user->id) {
            throw new \RuntimeException('Forbidden');
        }

        $buyer->name    = $data['name'];
        $buyer->phone   = $data['phone'];
        $buyer->address = $data['address'];

        if ($photoPath !== null) {
            if ($buyer->photo_path) {
                Storage::disk('public')->delete($buyer->photo_path);
            }
            $buyer->photo_path = $photoPath;
        }

        $buyer->save();

        return $buyer;
    }

    public function updateSellerProfile(User $user, Seller $seller, array $data, ?string $photoPath = null): Seller
    {
        if ((int) $seller->user_id !== (int) $user->id) {
            throw new \RuntimeException('Forbidden');
        }

        if (! empty($data['store_name'])) {
            $seller->store_name = $data['store_name'];
        }
        $seller->phone   = $data['phone'];
        $seller->address = $data['address'];

        if ($photoPath !== null) {
            if ($seller->photo_path) {
                Storage::disk('public')->delete($seller->photo_path);
            }
            $seller->photo_path = $photoPath;
        }

        $seller->save();

        return $seller;
    }
}

