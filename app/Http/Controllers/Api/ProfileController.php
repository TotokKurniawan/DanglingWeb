<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Buyer;
use App\Models\Seller;
use App\Services\Api\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProfileService $profileService,
    ) {}

    public function updateBuyerProfile(Request $request, $id)
    {
        $buyer = Buyer::find($id);
        if (!$buyer) {
            return $this->error('Buyer not found', 404);
        }

        $authUser = $request->user();
        if (!$authUser || (int) $buyer->user_id !== (int) $authUser->id) {
            return $this->error('Forbidden', 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('buyers', 'public')
            : null;

        $buyer = $this->profileService->updateBuyerProfile($authUser, $buyer, $request->only(['name','phone','address']), $photoPath);

        $data = $buyer->toArray();
        $data['photo_url'] = $buyer->photo_path ? url('storage/' . $buyer->photo_path) : null;
        return $this->success(['buyer' => $data], 'Buyer profile updated successfully', 200);
    }

    public function updateSellerProfile(Request $request, $id)
    {
        $seller = Seller::find($id);
        if (!$seller) {
            return $this->error('Seller not found', 404);
        }

        $authUser = $request->user();
        if (!$authUser || (int) $seller->user_id !== (int) $authUser->id) {
            return $this->error('Forbidden', 403);
        }

        $request->validate([
            'store_name' => 'sometimes|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('sellers', 'public')
            : null;

        $seller = $this->profileService->updateSellerProfile($authUser, $seller, $request->only(['store_name','phone','address']), $photoPath);

        $data = $seller->toArray();
        $data['photo_url'] = $seller->photo_path ? url('storage/' . $seller->photo_path) : null;
        return $this->success(['seller' => $data], 'Seller profile updated successfully', 200);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();
        if ($user->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo_path);
        }

        $path = $request->file('photo')->store('users', 'public');
        $user->photo_path = $path;
        $user->save();

        return $this->success([
            'photo_url' => url('storage/' . $path)
        ], 'Profile photo updated successfully', 200);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Cabut semua token sesi agar ter-logout
        $user->tokens()->delete();

        // Hapus hard delete user (cascade menyesuaikan DB jika foreign key cascade)
        // Atau akan dibiarkan tertahan jika constrain membatasi.
        $user->delete();

        return $this->success(null, 'Account deleted successfully', 200);
    }
}
