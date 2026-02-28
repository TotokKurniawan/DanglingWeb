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
            'deskripsi' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'id_pembeli' => 'nullable|exists:pembelis,id',
            'id_pedagang' => 'nullable|exists:pedagangs,id',
        ];
    }
}
