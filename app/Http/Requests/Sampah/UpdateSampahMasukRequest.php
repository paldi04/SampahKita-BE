<?php

namespace App\Http\Requests\Sampah;

use App\Models\SampahKategori;
use App\Models\TempatTimbulanSampah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSampahMasukRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge([
            'tts_id' => auth()->user()->tts_id
        ]);
        return auth()->user()->user_role_id === 'oss' && auth()->user()->status === 'verified';
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
            'tts_id' => [
                'required',
                'string',
                'exists:' . TempatTimbulanSampah::class . ',id', // Ensure the ID exists
            ],
            'sampah_kategori_id' => [
                'required',
                'numeric',
                'exists:' . SampahKategori::class . ',id', // Ensure the ID exists
            ],
            'foto_sampah' => 'required|string',
            'waktu_masuk' => 'required|date_format:Y-m-d H:i:s',
            'berat_kg' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'tts_id.exists' => 'ID tempat timbulan sampah tidak valid!',
            'sampah_kategori_id.exists' => 'ID kategori sampah tidak valid!',
            'foto_sampah.required' => 'Foto sampah wajib diisi!',
            'waktu_masuk.required' => 'Waktu masuk wajib diisi!',
            'waktu_masuk.date_format' => 'Format waktu masuk tidak valid! (Contoh: 2021-12-31 23:59:59)',
            'berat_kg.required' => 'Berat sampah wajib diisi!',
            'berat_kg.numeric' => 'Berat sampah harus berupa angka!'
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
