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
            'rating' => 'required|integer|min:1|max:5',
            'seller_id' => 'nullable|exists:sellers,id',
            'validate_order' => 'nullable|boolean',
        ];
    }
}
