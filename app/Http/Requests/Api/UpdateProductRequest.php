<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user || !$user->seller) {
            return false;
        }
        $id = $this->route('id');
        if (!$id) {
            return false;
        }
        $product = \App\Models\Product::find($id);
        return $product && (int) $product->id_pedagang === (int) $user->seller->id;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'sometimes|string|max:255',
            'harga_produk' => 'sometimes|numeric|min:0',
            'kategori_produk' => 'sometimes|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
