<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\StoreComplaintWebRequest;
use App\Services\Web\ComplaintService;

class ComplaintController extends Controller
{
    public function __construct(
        protected ComplaintService $complaintService,
    ) {}

    public function storeComplaint(StoreComplaintWebRequest $request)
    {
        $validated = $request->validated();
        $this->complaintService->submitWebComplaint([
            'description' => $validated['description'],
            'rating' => $validated['rating'],
            'buyer_id' => $validated['buyer_id'] ?? null,
            'seller_id' => $validated['seller_id'] ?? null,
        ]);

        session()->flash('success', 'Complaint submitted successfully.');
        return redirect()->back()->with('success', 'Complaint submitted successfully.');
    }
}
