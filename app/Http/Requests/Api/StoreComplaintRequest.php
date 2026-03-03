<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string|max:2000',
            'rating'      => 'required|integer|min:1|max:5',
            'seller_id'   => 'required|exists:sellers,id',
            'order_id'    => 'required|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'seller_id.required' => 'Seller ID wajib diisi.',
            'order_id.required'  => 'Order ID wajib diisi.',
            'order_id.exists'    => 'Order tidak ditemukan.',
        ];
    }
}
