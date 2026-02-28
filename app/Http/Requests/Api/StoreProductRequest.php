<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->seller;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'harga_produk' => 'required|numeric|min:0',
            'kategori_produk' => 'required|string|max:100',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
