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
        $this->merge(['id' => $this->route('id')]);
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'phone_number' => 'string|regex:/^[0-9]{7,15}$/|starts_with:08',
            'email' => 'email',
            'status' => 'in:verified,unverified,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Format nomor telepon tidak valid!',
            'phone_number.starts_with' => 'Nomor telepon harus diawali dengan 08!',
            'status.in' => 'Status tidak valid!',
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
