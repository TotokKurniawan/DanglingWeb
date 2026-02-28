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
            'deskripsi' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'id_pedagang' => 'nullable|exists:pedagangs,id',
            'validate_order' => 'nullable|boolean',
        ];
    }
}
