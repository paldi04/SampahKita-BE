<?php

namespace App\Http\Requests\TempatTimbulanSampah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetTempatTimbulanSampahDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge(['id' => $this->route('id')]);
        if ($this->user()->user_role_id === 'admin') {
            return true;
        }
        return $this->user()->tts_id === $this->route('id');
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
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID tidak boleh kosong!',
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
