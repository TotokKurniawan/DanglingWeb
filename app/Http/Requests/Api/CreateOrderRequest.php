<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user || !$user->buyer) {
            return false;
        }
        return (int) $this->input('buyer_id') === (int) $user->buyer->id;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|max:255',
            'buyer_id' => 'required|exists:buyers,id',
            'seller_id' => 'required|exists:sellers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal satu item produk harus dipilih.',
        ];
    }
}
