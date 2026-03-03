<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\Api\SellerService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected SellerService $sellerService,
    ) {}

    public function updateLocation(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser || !$authUser->seller) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $authUser->seller->id !== (int) $id) {
            return $this->error('Forbidden', 403);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $this->sellerService->updateLocation($authUser, (float) $request->latitude, (float) $request->longitude);

        return $this->success(null, 'Seller location updated successfully', 200);
    }

    public function updateLocationMe(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser || !$authUser->seller) {
            return $this->error('Forbidden', 403);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $this->sellerService->updateLocation($authUser, (float) $request->latitude, (float) $request->longitude);

        return $this->success(null, 'Seller location updated successfully', 200);
    }
}
