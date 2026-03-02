<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Seller;
use App\Services\Api\SellerService;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected SellerService $sellerService,
    ) {}

    public function upgradeToSeller(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $authUser = $request->user();
        if (!$authUser) {
            return $this->error('User not authenticated', 401);
        }

        if ($request->filled('user_id') && (int) $request->user_id !== (int) $authUser->id) {
            return $this->error('Forbidden', 403);
        }

        $seller = new Seller();
        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('sellers', 'public')
            : null;

        try {
            $seller = $this->sellerService->upgradeToSeller($authUser, $request->only(['store_name', 'phone', 'address']), $photoPath);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Already registered as a seller') {
                return $this->error($e->getMessage(), 409);
            }
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['seller_id' => $seller->id], 'Seller registered successfully', 201);
    }

    public function getStoreStatus(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $isOnline = $this->sellerService->getStoreStatus($user);
        return $this->success(['is_online' => $isOnline], 'Success', 200);
    }

    public function updateStoreStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,offline',
        ]);

        $authUser = $request->user();
        if (!$authUser) {
            return $this->error('User not authenticated', 401);
        }

        if ($request->filled('user_id') && (int) $request->user_id !== (int) $authUser->id) {
            return $this->error('Forbidden', 403);
        }

        try {
            $status = $this->sellerService->updateStoreStatus($authUser, $request->status);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Store not found') {
                return $this->error($e->getMessage(), 404);
            }
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['status' => $status], 'Store status updated', 200);
    }
}
