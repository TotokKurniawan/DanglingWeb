<?php

namespace App\Http\Controllers\Web;

use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function updateSellerStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pedagangs,id',
        ]);

        $seller = Seller::find($request->id);
        if ($seller) {
            $seller->status = $seller->status === 'offline' ? 'online' : 'offline';
            $seller->save();
            return redirect()->back()->with('message', 'Status updated successfully');
        }
        return redirect()->back()->with('error', 'Seller not found');
    }
}
