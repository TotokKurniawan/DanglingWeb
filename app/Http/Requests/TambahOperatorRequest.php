<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TambahOperatorRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Pastikan email unik
            'password' => 'required|string', // Minimal panjang password
            'role' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi foto sebagai file gambar
        ];
    }
}
