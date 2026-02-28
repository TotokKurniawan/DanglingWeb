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
        return (int) $this->input('id_pembeli') === (int) $user->buyer->id;
    }

    public function rules(): array
    {
        return [
            'bentuk_pembayaran' => 'required|string|max:255',
            'id_pembeli' => 'required|exists:pembelis,id',
            'id_pedagang' => 'required|exists:pedagangs,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal satu item produk harus dipilih.',
        ];
    }
}
