<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Hanya seller pemilik produk yang boleh mengubah / menghapus.
     */
    public function manage(User $user, Product $product): bool
    {
        return $user->hasRole('seller')
            && $user->seller
            && $user->seller->id === $product->seller_id;
    }
}

