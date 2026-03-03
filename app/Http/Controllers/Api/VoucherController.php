<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/orders/check-voucher — mengecek ketersediaan voucher dan hitung estimasi diskon.
     */
    public function check(Request $request)
    {
        $request->validate([
            'code'     => 'required|string',
            'subtotal' => 'required|integer|min:1',
        ]);

        $voucher = Voucher::activeAndValid()
            ->where('code', $request->code)
            ->first();

        if (! $voucher) {
            return $this->error('Voucher tidak ditemukan, invalid, atau sudah habis kuotanya.', 404);
        }

        if ($request->subtotal < $voucher->min_purchase) {
            return $this->error("Minimal pembelian untuk voucher ini adalah Rp {$voucher->min_purchase}.", 422);
        }

        $discountInfo = [
            'discount_amount' => $voucher->calculateDiscount((int)$request->subtotal),
            'voucher_code'    => $voucher->code,
        ];

        return $this->success($discountInfo, 'Voucher dapat digunakan.', 200);
    }
}
