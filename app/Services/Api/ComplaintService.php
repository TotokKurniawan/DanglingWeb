<?php

namespace App\Services\Api;

use App\Models\ActivityLog;
use App\Models\Complaint;
use App\Models\Order;
use App\Models\User;

class ComplaintService
{
    public function __construct(
        protected SellerRatingService $ratingService,
    ) {}

    /**
     * Submit keluhan/rating oleh buyer (authenticated).
     *
     * Validasi:
     * - Buyer harus punya buyer profile
     * - Order harus milik buyer dan seller yang dituju
     * - Belum pernah submit complaint untuk order yang sama
     */
    public function submitApiComplaint(User $user, array $data): Complaint
    {
        $buyer = $user->buyer;
        if (! $buyer) {
            throw new \RuntimeException('User does not have a buyer profile.');
        }

        $buyerId  = $buyer->id;
        $sellerId = (int) $data['seller_id'];
        $orderId  = (int) $data['order_id'];

        // Validasi: order harus milik buyer DAN seller yang dituju
        $order = Order::where('id', $orderId)
            ->where('buyer_id', $buyerId)
            ->where('seller_id', $sellerId)
            ->first();

        if (! $order) {
            throw new \RuntimeException('Order tidak ditemukan atau bukan milik Anda dengan seller ini.');
        }

        // Validasi: order harus sudah completed agar boleh di-review
        if ($order->status !== Order::STATUS_COMPLETED) {
            throw new \RuntimeException('Hanya order yang sudah selesai yang boleh diberi rating.');
        }

        // Validasi: 1 complaint/rating per order per buyer
        $exists = Complaint::where('buyer_id', $buyerId)
            ->where('order_id', $orderId)
            ->exists();

        if ($exists) {
            throw new \RuntimeException('Anda sudah memberikan rating untuk order ini.');
        }

        $complaint = Complaint::create([
            'description' => $data['description'],
            'rating'      => $data['rating'],
            'status'      => Complaint::STATUS_OPEN,
            'buyer_id'    => $buyerId,
            'seller_id'   => $sellerId,
            'order_id'    => $orderId,
        ]);

        // Update rating agregat seller
        $this->ratingService->recalculate($sellerId);

        ActivityLog::log('complaint.submitted', $complaint, [
            'buyer_id'  => $buyerId,
            'seller_id' => $sellerId,
            'order_id'  => $orderId,
            'rating'    => $data['rating'],
        ]);

        return $complaint;
    }
}
