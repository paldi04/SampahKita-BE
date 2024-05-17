<?php

namespace App\Http\Requests\TempatTimbulanSampah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetTempatTimbulanSampahSektorListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'tts_kategori_id' => 'required:exists:tempat_timbulan_sampah_kategoris,id',
            'page' => 'numeric|min:1',
            'size' => 'numeric|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'tts_kategori_id.required' => 'Kategori Tempat Timbulan Sampah tidak boleh kosong!',
            'page.min' => 'Minimum halaman adalah 1!',
            'size.min' => 'Minimum ukuran adalah 1!',
            'size.max' => 'Maksimum ukuran adalah 100!',
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
