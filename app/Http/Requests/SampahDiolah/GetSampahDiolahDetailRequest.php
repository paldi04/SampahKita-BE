<?php

namespace App\Http\Requests\SampahDiolah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetSampahDiolahDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge(['id' => $this->route('id')]);
        if (auth()->user()->user_role_id === 'oss') {
            $this->merge(['tss_id' => auth()->user()->tts_id]);
        }
        if (auth()->user()->user_role_id === 'oks') {
            $this->merge(['tks_id' => auth()->user()->tts_id]);
        }
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
            'id' => 'required|string', // Add 'id' field validation
            'tts_id' => 'nullable|string',
            'tks_id' => 'nullable|string',
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
