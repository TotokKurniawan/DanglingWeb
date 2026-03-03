<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/device-token — simpan atau update device token FCM.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token'    => 'required|string|max:500',
            'platform' => 'nullable|in:android,ios',
        ]);

        $user = $request->user();
        if (! $user) {
            return $this->error('Unauthenticated', 401);
        }

        $deviceToken = DeviceToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'token'   => $request->token,
            ],
            [
                'platform'  => $request->input('platform', 'android'),
                'is_active' => true,
            ]
        );

        return $this->success(['device_token' => $deviceToken], 'Device token saved', 200);
    }

    /**
     * DELETE /api/device-token — nonaktifkan device token (saat logout).
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:500',
        ]);

        $user = $request->user();
        if (! $user) {
            return $this->error('Unauthenticated', 401);
        }

        DeviceToken::where('user_id', $user->id)
            ->where('token', $request->token)
            ->update(['is_active' => false]);

        return $this->success([], 'Device token removed', 200);
    }
}
