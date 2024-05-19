<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'required|string',
            'new_password' => 'required|string|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])[\s\S]{6,}$/',
            'confirm_password' => 'required|same:new_password',
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required' => 'Kolom kata sandi lama wajib diisi.',
            'new_password.required' => 'Kolom kata sandi baru wajib diisi.',
            'new_password.regex' => 'Kolom kata sandi baru minimal 4 karakter, terdiri dari huruf besar, huruf kecil, angka, dan karakter spesial.',
            'confirm_password.required' => 'Kolom konfirmasi kata sandi wajib diisi.',
            'confirm_password.same' => 'Password baru dan konfirmasi password tidak sama.'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Kesalahan validasi!',
            'data'    => $validator->errors()
        ], 400));
    }
}
