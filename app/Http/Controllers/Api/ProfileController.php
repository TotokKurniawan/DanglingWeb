<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Buyer;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse;

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
            'nama' => 'required|string|max:255',
            'telfon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($buyer->foto) {
                Storage::disk('public')->delete($buyer->foto);
            }
            $buyer->foto = $request->file('foto')->store('foto_pembelis', 'public');
        }

        $buyer->nama = $request->nama;
        $buyer->telfon = $request->telfon;
        $buyer->alamat = $request->alamat;
        $buyer->save();

        $data = $buyer->toArray();
        $data['foto_url'] = $buyer->foto ? url('storage/' . $buyer->foto) : null;
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

        // Terima nama_toko, namaToko (API), atau nama (backward compat) → simpan ke namaToko
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'nama_toko' => 'sometimes|string|max:255',
            'namaToko' => 'sometimes|string|max:255',
            'telfon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($seller->foto) {
                Storage::disk('public')->delete($seller->foto);
            }
            $seller->foto = $request->file('foto')->store('foto_pedagangs', 'public');
        }

        $namaToko = $request->input('nama_toko') ?? $request->input('namaToko') ?? $request->input('nama');
        if ($namaToko !== null) {
            $seller->namaToko = $namaToko;
        }
        $seller->telfon = $request->telfon;
        $seller->alamat = $request->alamat;
        $seller->save();

        $data = $seller->toArray();
        $data['foto_url'] = $seller->foto ? url('storage/' . $seller->foto) : null;
        return $this->success(['seller' => $data], 'Seller profile updated successfully', 200);
    }
}
