<?php

namespace App\Http\Requests\SampahDiolah;

use App\Models\SampahKategori;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetPermintaanSampahDiolahListRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (auth()->user()->user_role_id !== 'admin') {
            $this->merge(['tts_tujuan_id' => $this->user()->tts_id]);
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
            'tts_tujuan_id' => 'required|string',
            'tts_id' => 'nullable|string',
            'sampah_kategori_id' => [
                'numeric',
                'exists:' . SampahKategori::class . ',id', // Ensure the ID exists
            ],
            'status' => 'string',
            'page' => 'numeric|min:1',
            'size' => 'numeric|min:1|max:100',
            'start_date' => 'date_format:Y-m-d',
            'end_date' => 'date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'sampah_kategori_id.exists' => 'ID kategori sampah tidak valid!',
            'page.min' => 'Minimum halaman adalah 1!',
            'size.min' => 'Minimum ukuran adalah 1!',
            'size.max' => 'Maksimum ukuran adalah 100!',
            'start_date.date_format' => 'Format tanggal awal tidak valid! (Contoh: 2024-05-01)',
            'end_date.date_format' => 'Format tanggal akhir tidak valid! (Contoh: 2024-05-31)',
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
