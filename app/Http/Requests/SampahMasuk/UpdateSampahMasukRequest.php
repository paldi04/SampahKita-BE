<?php

namespace App\Http\Requests\SampahMasuk;

use App\Models\SampahKategori;
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
        $this->merge(['id' => $this->route('id')]);
        if (auth()->user()->user_role_id === 'admin') {
            return true;
        }
        if (auth()->user()->user_role_id === 'oss' && auth()->user()->status === 'verified') {
            $this->merge(['tts_id' => auth()->user()->tts_id]);
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
            'sampah_kategori_id' => [
                'required',
                'numeric',
                'exists:' . SampahKategori::class . ',id', // Ensure the ID exists
            ],
            'foto_sampah' => 'string',
            'waktu_masuk' => 'date_format:Y-m-d H:i:s',
            'berat_kg' => 'numeric',
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
