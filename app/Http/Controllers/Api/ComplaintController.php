<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreComplaintRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Complaint;
use App\Models\Order;

class ComplaintController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/complaints — submit keluhan (auth required).
     * Jika id_pedagang diisi dan user adalah pembeli, validasi opsional: pernah ada order dengan seller tersebut.
     */
    public function store(StoreComplaintRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $idPembeli = null;

        if ($user && $user->buyer) {
            $idPembeli = $user->buyer->id;
            $idPedagang = $data['id_pedagang'] ?? null;

            if ($idPedagang && $request->boolean('validate_order')) {
                $hasOrder = Order::where('id_pembeli', $idPembeli)
                    ->where('id_pedagang', $idPedagang)
                    ->exists();
                if (!$hasOrder) {
                    return $this->error('Anda belum pernah melakukan order dengan seller ini', 422);
                }
            }
        }

        $complaint = Complaint::create([
            'deskripsi' => $data['deskripsi'],
            'rating' => $data['rating'],
            'id_pembeli' => $idPembeli,
            'id_pedagang' => $data['id_pedagang'] ?? null,
        ]);

        return $this->success(['complaint' => $complaint], 'Keluhan berhasil dikirim', 201);
    }
}
