<?php

namespace App\Services\Web;

use App\Models\Complaint;

class ComplaintService
{
    public function submitWebComplaint(array $data): Complaint
    {
        return Complaint::create([
            'description' => $data['description'],
            'rating'      => $data['rating'],
            'buyer_id'    => $data['buyer_id'] ?? null,
            'seller_id'   => $data['seller_id'] ?? null,
        ]);
    }
}

