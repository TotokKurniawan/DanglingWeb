<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\BuyerFavorite;
use App\Models\Seller;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/favorites — daftar seller favorit buyer.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $buyer = $user->buyer;
        if (! $buyer) {
            return $this->error('Buyer profile not found', 403);
        }

        $favorites = BuyerFavorite::where('buyer_id', $buyer->id)
            ->with(['seller' => fn ($q) => $q->select(
                'id', 'store_name', 'phone', 'address', 'photo_path',
                'is_online', 'rating_average', 'rating_count', 'latitude', 'longitude'
            )])
            ->get()
            ->map(fn ($fav) => [
                'id'        => $fav->id,
                'seller_id' => $fav->seller_id,
                'seller'    => $fav->seller ? [
                    'id'             => $fav->seller->id,
                    'store_name'     => $fav->seller->store_name,
                    'phone'          => $fav->seller->phone,
                    'address'        => $fav->seller->address,
                    'photo_url'      => $fav->seller->photo_path ? url('storage/' . $fav->seller->photo_path) : null,
                    'is_online'      => $fav->seller->is_online,
                    'rating_average' => $fav->seller->rating_average,
                    'rating_count'   => $fav->seller->rating_count,
                ] : null,
                'added_at' => $fav->created_at->toIso8601String(),
            ]);

        return $this->success(['favorites' => $favorites], 'Success', 200);
    }

    /**
     * POST /api/favorites — tambah seller ke favorit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
        ]);

        $user = $request->user();
        $buyer = $user->buyer;
        if (! $buyer) {
            return $this->error('Buyer profile not found', 403);
        }

        $exists = BuyerFavorite::where('buyer_id', $buyer->id)
            ->where('seller_id', $request->seller_id)
            ->exists();

        if ($exists) {
            return $this->error('Seller sudah ada di daftar favorit', 409);
        }

        $favorite = BuyerFavorite::create([
            'buyer_id'  => $buyer->id,
            'seller_id' => (int) $request->seller_id,
        ]);

        return $this->success(['favorite' => $favorite], 'Seller berhasil ditambahkan ke favorit', 201);
    }

    /**
     * DELETE /api/favorites/{seller_id} — hapus seller dari favorit.
     */
    public function destroy(Request $request, $sellerId)
    {
        $user = $request->user();
        $buyer = $user->buyer;
        if (! $buyer) {
            return $this->error('Buyer profile not found', 403);
        }

        $deleted = BuyerFavorite::where('buyer_id', $buyer->id)
            ->where('seller_id', $sellerId)
            ->delete();

        if (! $deleted) {
            return $this->error('Favorite not found', 404);
        }

        return $this->success([], 'Seller berhasil dihapus dari favorit', 200);
    }
}
