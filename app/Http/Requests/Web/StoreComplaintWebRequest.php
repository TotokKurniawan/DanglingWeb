<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'buyer_id' => 'nullable|exists:buyers,id',
            'seller_id' => 'nullable|exists:sellers,id',
        ];
    }
}
