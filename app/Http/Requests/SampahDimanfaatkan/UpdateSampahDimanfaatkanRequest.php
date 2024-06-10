<?php

namespace App\Http\Requests\SampahDimanfaatkan;

use App\Models\SampahKategori;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSampahDimanfaatkanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge(['id' => $this->route('id')]);
        if (auth()->user()->user_role_id !== 'admin') {
            $this->merge(['tts_id' => auth()->user()->tts_id]);
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
            'id' => 'string', // Add 'id' field validation
            'tts_id' => 'nullable|string',
            'sampah_kategori_id' => [
                'numeric',
                'exists:' . SampahKategori::class . ',id', // Ensure the ID exists
            ],
            'berat_kg' => 'numeric',
            'nama_produk' => 'string',
            'nilai_jual' => 'numeric',
            'jumlah_produk' => 'numeric',
            'kategori_produk' => 'string',
            'foto_produk' => 'string',
            'kode_produk' => 'string',
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
