<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    use ApiResponse;

    public function upgradeToSeller(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required_without:namaToko|string|max:255',
            'namaToko' => 'required_without:nama_toko|string|max:255',
            'telfon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $authUser = $request->user();
        if (!$authUser) {
            return $this->error('User not authenticated', 401);
        }

        if ($request->filled('user_id') && (int) $request->user_id !== (int) $authUser->id) {
            return $this->error('Forbidden', 403);
        }

        if ($authUser->seller) {
            return $this->error('Already registered as a seller', 409);
        }

        $namaToko = $request->input('nama_toko') ?? $request->input('namaToko');
        $seller = new Seller();
        $seller->namaToko = $namaToko;
        $seller->telfon = $request->telfon;
        $seller->alamat = $request->alamat;
        $seller->status = 'online';
        $seller->user_id = $authUser->id;

        if ($request->hasFile('foto')) {
            $seller->foto = $request->file('foto')->store('pedagang', 'public');
        }
        $seller->save();

        if ($authUser->role !== 'pedagang') {
            $authUser->role = 'pedagang';
            $authUser->save();
        }

        return $this->success(['seller_id' => $seller->id], 'Seller registered successfully', 201);
    }

    public function getStoreStatus(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $isOnline = $user->seller && $user->seller->status === 'online';
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

        $seller = $authUser->seller;
        if (!$seller) {
            return $this->error('Store not found', 404);
        }

        $seller->status = $request->status;
        $seller->save();

        return $this->success(['status' => $seller->status], 'Store status updated', 200);
    }
}
