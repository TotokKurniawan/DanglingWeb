<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreComplaintRequest;
use App\Http\Traits\ApiResponse;
use App\Services\Api\ComplaintService;

class ComplaintController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ComplaintService $complaintService,
    ) {}

    /**
     * POST /api/complaints — submit keluhan (auth required).
     * Jika id_pedagang diisi dan user adalah pembeli, validasi opsional: pernah ada order dengan seller tersebut.
     */
    public function store(StoreComplaintRequest $request)
    {
        $data = $request->validated();
        try {
            $complaint = $this->complaintService->submitApiComplaint($request->user(), $data);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['complaint' => $complaint], 'Keluhan berhasil dikirim', 201);
    }
}
