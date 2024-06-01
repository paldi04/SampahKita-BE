<?php

namespace App\Http\Requests\User;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetUserListRequest extends FormRequest
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
            'page' => 'numeric|min:1',
            'size' => 'numeric|min:1|max:100',
            'user_role_id' => 'string|exists:user_roles,id',
            'status' => 'string|in:terverifikasi,belum_terverifikasi',
        ];
    }

    public function messages(): array
    {
        return [
            'page.min' => 'Minimum halaman adalah 1!',
            'size.min' => 'Minimum ukuran adalah 1!',
            'size.max' => 'Maksimum ukuran adalah 100!',
            'user_role_id.exists' => 'Role user tidak ditemukan!',
            'status.in' => 'Status tidak valid!'
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
