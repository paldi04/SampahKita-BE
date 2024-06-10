<?php

namespace App\Http\Requests\SampahDiolah;

use App\Models\SampahKategori;
use App\Models\TempatTimbulanSampah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSampahDiolahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge(['tss_id' => auth()->user()->tts_id]);
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
            'tss_id' => [
                'required',
                'string',
                'exists:' . TempatTimbulanSampah::class . ',id', // Ensure the ID exists
            ],
            'sampah_kategori_id' => [
                'required',
                'numeric',
                'exists:' . SampahKategori::class . ',id', // Ensure the ID exists
            ],
            'berat_kg' => 'required|numeric',
            'diolah_oleh' => 'required|string',
            'tks_id' => $this->input('diolah_oleh') === 'tks' ? 'required|exists:' . TempatTimbulanSampah::class . ',id' : 'nullable',
            'waktu_diolah' => 'required|date_format:Y-m-d H:i:s'

        ];
    }

    public function messages(): array
    {
        return [
            'tts_id.required' => 'ID Tempat Sumber Sampah wajib diisi!',
            'tts_id.string' => 'ID Tempat Sumber Sampah harus berupa string!',
            'tts_id.exists' => 'ID Tempat Sumber Sampah tidak ditemukan!',
            'sampah_kategori_id.required' => 'ID Kategori Sampah wajib diisi!',
            'sampah_kategori_id.numeric' => 'ID Kategori Sampah harus berupa angka!',
            'sampah_kategori_id.exists' => 'ID Kategori Sampah tidak ditemukan!',
            'berat_kg.required' => 'Berat Sampah wajib diisi!',
            'berat_kg.numeric' => 'Berat Sampah harus berupa angka!',
            'diolah_oleh.required' => 'Diolah Oleh wajib diisi!',
            'diolah_oleh.string' => 'Diolah Oleh harus berupa string!',
            'tks_id.exists' => 'ID Tempat Kumpulan Sampah tidak ditemukan!',
            'waktu_diolah.required' => 'Waktu Pengolahan Sampah wajib diisi!',
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
