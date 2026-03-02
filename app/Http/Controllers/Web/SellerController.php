<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\UpdateSellerStatusRequest;
use App\Models\Seller;
use App\Services\Web\SellerWebService;

class SellerController extends Controller
{
    public function __construct(
        protected SellerWebService $sellerWebService,
    ) {}

    public function updateSellerStatus(UpdateSellerStatusRequest $request)
    {
        $seller = Seller::find($request->id);
        if (! $seller) {
            return redirect()->back()->with('error', 'Seller not found');
        }

        $this->sellerWebService->toggleStatus($seller);

        return redirect()->back()->with('message', 'Status updated successfully');
    }
}
