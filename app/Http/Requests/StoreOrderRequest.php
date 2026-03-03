<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'seller_id'           => 'required|exists:sellers,id',
            'payment_method'      => 'required|in:COD,TRANSFER',
            'notes'               => 'nullable|string',
            'voucher_code'        => 'nullable|string|exists:vouchers,code',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
        ];
    }
}
