<?php

namespace App\Services\Api;

use App\Models\Complaint;
use App\Models\Order;
use App\Models\User;

class ComplaintService
{
    public function submitApiComplaint(User $user, array $data): Complaint
    {
        $buyerId  = null;
        $sellerId = $data['seller_id'] ?? null;

        if ($user->buyer) {
            $buyerId = $user->buyer->id;

            if ($sellerId && ! empty($data['validate_order'])) {
                $hasOrder = Order::where('buyer_id', $buyerId)
                    ->where('seller_id', $sellerId)
                    ->exists();

                if (! $hasOrder) {
                    throw new \RuntimeException('You have not ordered from this seller yet.');
                }
            }
        }

        return Complaint::create([
            'description' => $data['description'],
            'rating'      => $data['rating'],
            'buyer_id'    => $buyerId,
            'seller_id'   => $sellerId,
        ]);
    }
}

