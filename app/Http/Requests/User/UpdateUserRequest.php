<?php

namespace App\Http\Requests\User;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
            'fullname' => 'required|string|max:255',
            'phone_number' => 'required|string|regex:/^[0-9]{7,15}$/|starts_with:08',
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Format nomor telepon tidak valid!',
            'phone_number.starts_with' => 'Nomor telepon harus diawali dengan 08!',
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
