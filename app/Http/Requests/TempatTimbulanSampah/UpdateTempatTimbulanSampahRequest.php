<?php

namespace App\Http\Requests\TempatTimbulanSampah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTempatTimbulanSampahRequest extends FormRequest
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
            'nama_tempat' => 'string|max:255',
            'alamat_tempat' => 'string|max:255',
            'afiliasi' => 'nullable|string|max:255',
            'latitude' => 'string',
            'longitude' => 'string',
            'luas_lahan' => 'numeric',
            'luas_bangunan' => 'numeric',
            'panjang' => 'numeric',
            'lebar' => 'numeric',
            'sisa_lahan' => 'numeric',
            'kepemilikan_lahan' => 'string|max:255',
            'foto_tempat' => 'nullable|array',
            'foto_tempat.*' => 'nullable|string',
            'status' => 'string|in:active,inactive'
        ];
    }

    public function messages(): array
    {
        return [
            'nama_tempat.max' => 'Nama tidak boleh lebih dari 255 karakter!',
            'tts_kategori_id.exists' => 'ID kategori tempat timbulan sampah tidak valid!',
            'tts_sektor_id.exists' => 'ID sektor tempat timbulan sampah tidak valid!',
            'alamat_tempat.max' => 'Alamat tidak boleh lebih dari 255 karakter!',
            'afiliasi.max' => 'Afiliasi tidak boleh lebih dari 255 karakter!',
            'kepemilikan_lahan.max' => 'Kepemilikan lahan tidak boleh lebih dari 255 karakter!',
            'latitude' => 'Latitude harus berupa string!',
            'longitude' => 'Longitude harus berupa string!',
            'luas_lahan' => 'Luas lahan harus berupa angka!',
            'luas_bangunan' => 'Luas bangunan harus berupa angka!',
            'panjang' => 'Panjang harus berupa angka!',
            'lebar' => 'Lebar harus berupa angka!',
            'sisa_lahan' => 'Sisa lahan harus berupa angka!',
            'foto_tempat.array' => 'Foto tempat harus berupa array!',
            'foto_tempat.*.string' => 'Foto tempat harus berupa string!',
            'status.in' => 'Status harus berupa active atau inactive!'
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
