<?php

namespace App\Http\Requests\SampahDiolah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSampahDiolahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge(['id' => $this->route('id')]);
        if (auth()->user()->user_role_id === 'admin') {
            return true;
        }
        if (auth()->user()->user_role_id === 'oss' && auth()->user()->status === 'terverifikasi') {
            $this->merge(['tss_id' => auth()->user()->tts_id]);
            return true;
        }
        return false;
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
            'tss_id' => 'nullable|string',
            'status' => 'string|in:menunggu_respon,sudah_direspon,dibatalkan', // Add 'status' field validation
            'keterangan' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID tidak boleh kosong!',
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
