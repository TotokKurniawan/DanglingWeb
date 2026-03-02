<?php

namespace App\Services\Web;

use App\Models\Seller;

class SellerWebService
{
    public function toggleStatus(Seller $seller): Seller
    {
        $seller->status = $seller->status === 'offline' ? 'online' : 'offline';
        $seller->save();

        return $seller;
    }
}

