<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\StoreComplaintWebRequest;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    public function storeComplaint(StoreComplaintWebRequest $request)
    {
        $validated = $request->validated();
        Complaint::create([
            'deskripsi' => $validated['deskripsi'],
            'rating' => $validated['rating'],
            'id_pembeli' => $validated['id_pembeli'] ?? null,
            'id_pedagang' => $validated['id_pedagang'] ?? null,
        ]);

        session()->flash('success', 'Complaint submitted successfully.');
        return redirect()->back()->with('success', 'Complaint submitted successfully.');
    }
}
