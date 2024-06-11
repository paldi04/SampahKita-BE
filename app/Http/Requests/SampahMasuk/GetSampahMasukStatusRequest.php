<?php

namespace App\Http\Requests\SampahMasuk;

use App\Models\SampahKategori;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetSampahMasukStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user()->role != 'admin') {
            $this->merge(['tts_id' => $this->user()->tts_id]);
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
            'tts_id' => 'required|string',
            'sampah_kategori_id' => [
                'numeric',
                'exists:' . SampahKategori::class . ',id', // Ensure the ID exists
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'sampah_kategori_id.exists' => 'ID kategori sampah tidak valid!'
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
