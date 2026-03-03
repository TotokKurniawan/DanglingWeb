<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Seller pemilik order boleh mengelola status order.
     */
    public function manageAsSeller(User $user, Order $order): bool
    {
        return $user->hasRole('seller')
            && $user->seller
            && $user->seller->id === $order->seller_id;
    }

    /**
     * Buyer pemilik order boleh membatalkan order ketika masih pending.
     */
    public function cancelAsBuyer(User $user, Order $order): bool
    {
        if (! $user->hasRole('buyer') || ! $user->buyer) {
            return false;
        }

        return $user->buyer->id === $order->buyer_id
            && $order->canBeCancelledByBuyer();
    }
}

