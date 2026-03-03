<?php

namespace App\Services\Api;

use App\Models\Review;
use App\Models\Seller;

class SellerRatingService
{
    /**
     * Hitung ulang rating_average dan rating_count seller
     * berdasarkan semua review yang valid (punya rating).
     */
    public function recalculate(int $sellerId): void
    {
        $seller = Seller::find($sellerId);
        if (! $seller) {
            return;
        }

        $stats = Review::where('seller_id', $sellerId)
            ->whereNotNull('rating')
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $seller->rating_average = $stats->avg_rating ? round($stats->avg_rating, 2) : 0;
        $seller->rating_count   = (int) $stats->total;
        $seller->save();
    }
}
