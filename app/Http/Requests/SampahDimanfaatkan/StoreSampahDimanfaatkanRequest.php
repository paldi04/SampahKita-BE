<?php

namespace App\Http\Requests\SampahDimanfaatkan;

use App\Models\SampahKategori;
use App\Models\TempatTimbulanSampah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSampahDimanfaatkanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge(['tts_id' => auth()->user()->tts_id]);
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
            'berat_kg' => 'required|numeric',
            'nama_produk' => 'required|string',
            'nilai_jual' => 'required|numeric',
            'jumlah_produk' => 'required|numeric',
            'kategori_produk' => 'required|string',
            'foto_produk' => 'required|string',
            'kode_produk' => 'required|string',
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
            'nama_produk.required' => 'Nama Produk wajib diisi!',
            'nama_produk.string' => 'Nama Produk harus berupa string!',
            'nilai_jual.required' => 'Nilai Jual wajib diisi!',
            'nilai_jual.numeric' => 'Nilai Jual harus berupa angka!',
            'jumlah_produk.required' => 'Jumlah Produk wajib diisi!',
            'jumlah_produk.numeric' => 'Jumlah Produk harus berupa angka!',
            'kategori_produk.required' => 'Kategori Produk wajib diisi!',
            'kategori_produk.string' => 'Kategori Produk harus berupa string!',
            'foto_produk.required' => 'Foto Produk wajib diisi!',
            'foto_produk.string' => 'Foto Produk harus berupa string!',
            'kode_produk.required' => 'Kode Produk wajib diisi!',
            'kode_produk.string' => 'Kode Produk harus berupa string!',
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
