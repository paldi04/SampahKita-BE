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
        $id = $this->route('id') == 'me' ? auth()->user()->id : $this->route('id');
        if (auth()->user()->user_role_id != 'admin' && $id != auth()->user()->id) {
            return false;
        }
        $this->merge(['id' => $id]);
        return true;
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
            'id' => 'required|string',
            'nama' => 'string|max:255',
            'nomor_telepon' => 'string|regex:/^[0-9]{7,15}$/|starts_with:08',
            'email' => 'email',
            'status' => 'in:terverifikasi,belum_terverifikasi,ditolak',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_telepon.regex' => 'Format nomor telepon tidak valid!',
            'nomor_telepon.starts_with' => 'Nomor telepon harus diawali dengan 08!',
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
