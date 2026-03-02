<?php

namespace App\Services\Api;

use App\Models\Seller;
use App\Models\User;

class SellerService
{
    public function upgradeToSeller(User $user, array $data, ?string $photoPath = null): Seller
    {
        if ($user->seller) {
            throw new \RuntimeException('Already registered as a seller');
        }

        $seller = new Seller();
        $seller->store_name = $data['store_name'];
        $seller->phone      = $data['phone'];
        $seller->address    = $data['address'];
        $seller->status     = 'online';
        $seller->user_id    = $user->id;

        if ($photoPath) {
            $seller->photo_path = $photoPath;
        }

        $seller->save();

        if (! $user->hasRole('seller')) {
            $user->assignRole('seller');
        }

        return $seller;
    }

    public function getStoreStatus(User $user): bool
    {
        $seller = $user->seller;
        return $seller && $seller->status === 'online';
    }

    public function updateStoreStatus(User $user, string $status): string
    {
        $seller = $user->seller;
        if (! $seller) {
            throw new \RuntimeException('Store not found');
        }

        $seller->status = $status;
        $seller->save();

        return $seller->status;
    }

    public function updateLocation(User $user, float $latitude, float $longitude): void
    {
        $seller = $user->seller;
        if (! $seller) {
            throw new \RuntimeException('Forbidden');
        }

        $seller->latitude  = $latitude;
        $seller->longitude = $longitude;
        $seller->save();
    }
}

