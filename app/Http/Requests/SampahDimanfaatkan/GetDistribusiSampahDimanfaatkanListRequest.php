<?php

namespace App\Http\Requests\SampahDimanfaatkan;

use App\Models\SampahKategori;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetDistribusiSampahDimanfaatkanListRequest extends FormRequest
{
    public function authorize(): bool
    {
        $this->merge(['sampah_dimanfaatkan_id' => $this->route('id')]);
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
            'sampah_dimanfaatkan_id' => 'required|string',
            'sampah_kategori_id' => [
                'numeric',
                'exists:' . SampahKategori::class . ',id', // Ensure the ID exists
            ],
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
