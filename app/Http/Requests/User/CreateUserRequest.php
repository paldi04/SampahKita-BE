<?php

namespace App\Http\Requests\User;

use App\Models\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->user_role_id === 'admin';
    }
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Unauthorized',
            'data'    => []
        ], 400));
    }
    public function rules(): array
    {
        return [
            'user_role_id' => [
                'required',
                'string',
                'exists:' . UserRole::class . ',id', // Ensure the ID exists in the user_roles table
            ],
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|regex:/^[0-9]{7,15}$/|starts_with:08|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])[\s\S]{4,}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_telepon.regex' => 'Format nomor telepon tidak valid!',
            'nomor_telepon.starts_with' => 'Nomor telepon harus diawali dengan 08!',
            'username.regex' => 'Format username harus alfanumerik dan underscore saja!',
            'password.regex' => 'Format password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial!'
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
