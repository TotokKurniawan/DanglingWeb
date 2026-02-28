<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponse;

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

        $seller = $authUser->seller;
        $seller->latitude = $request->latitude;
        $seller->longitude = $request->longitude;
        $seller->save();

        return $this->success(null, 'Seller location updated successfully', 200);
    }
}
