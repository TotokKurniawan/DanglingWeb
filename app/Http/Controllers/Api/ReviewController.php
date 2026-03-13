<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use App\Models\Review;
use App\Services\Api\SellerRatingService;
use App\Services\FcmNotificationService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/sellers/{sellerId}/reviews — ambil daftar ulasan untuk toko tertentu.
     */
    public function indexBySeller($sellerId)
    {
        $reviews = Review::with(['buyer.user', 'order'])
            ->where('seller_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(['reviews' => $reviews], 'Success', 200);
    }

    /**
     * POST /api/reviews — pembeli submit review untuk pesanan yang sudah selesai.
     */
    public function store(StoreReviewRequest $request, SellerRatingService $ratingService, FcmNotificationService $fcmService)
    {
        $data = $request->validated();

        $user = $request->user();
        $buyer = $user->buyer;

        if (! $buyer) {
            return $this->error('Hanya pembeli yang bisa memberikan review.', 403);
        }

        $order = Order::with('seller.user')->find($data['order_id']);

        if ($order->buyer_id !== $buyer->id) {
            return $this->error('Bukan pesanan Anda.', 403);
        }

        if ($order->status !== Order::STATUS_COMPLETED) {
            return $this->error('Hanya pesanan selesai yang dapat direview.', 422);
        }

        if (Review::where('order_id', $order->id)->exists()) {
            return $this->error('Pesanan ini sudah pernah direview.', 422);
        }

        $review = Review::create([
            'order_id'  => $order->id,
            'buyer_id'  => $buyer->id,
            'seller_id' => $order->seller_id,
            'rating'    => $data['rating'],
            'comment'   => $data['comment'] ?? null,
        ]);

        $ratingService->recalculate($order->seller_id);

        // Notify seller via push & in-app
        if ($order->seller && $order->seller->user_id) {
            $fcmService->sendToUser($order->seller->user_id, 'Review Baru!', "Pesanan #{$order->id} mendapat rating {$data['rating']} bintang.", [
                'type'     => 'new_review',
                'order_id' => (string) $order->id,
            ]);
            \App\Models\UserNotification::send($order->seller->user_id, 'Review Baru!', "Pesanan #{$order->id} mendapat rating {$data['rating']} bintang.", 'review', [
                'order_id' => $order->id,
            ]);
        }

        return $this->success(['review' => $review], 'Review berhasil dikirim.', 201);
    }

    /**
     * POST /api/reviews/{id}/reply — seller membalas review.
     */
    public function reply(Request $request, $id, FcmNotificationService $fcmService)
    {
        $request->validate([
            'seller_reply' => 'required|string',
        ]);

        $user = $request->user();
        $seller = $user->seller;

        if (! $seller) {
            return $this->error('Hanya penjual yang bisa membalas review.', 403);
        }

        $review = Review::with(['order.buyer.user'])->find($id);

        if (! $review) {
            return $this->error('Review tidak ditemukan.', 404);
        }

        if ($review->seller_id !== $seller->id) {
            return $this->error('Bukan review toko Anda.', 403);
        }

        $review->update([
            'seller_reply' => $request->seller_reply,
        ]);

        // Notify buyer via push & in-app
        $buyerUserId = $review->order->buyer->user_id ?? null;
        if ($buyerUserId) {
            $fcmService->sendToUser($buyerUserId, 'Balasan Review', "Penjual membalas review pesanan #{$review->order_id}.", [
                'type'     => 'review_reply',
                'order_id' => (string) $review->order_id,
            ]);
            \App\Models\UserNotification::send($buyerUserId, 'Balasan Review', "Penjual membalas review pesanan #{$review->order_id}.", 'review_reply', [
                'order_id' => $review->order_id,
            ]);
        }

        return $this->success(['review' => $review], 'Balasan berhasil dikirim.', 200);
    }
}
