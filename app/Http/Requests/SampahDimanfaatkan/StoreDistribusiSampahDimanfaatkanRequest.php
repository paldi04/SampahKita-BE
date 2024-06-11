<?php

namespace App\Http\Requests\SampahDimanfaatkan;

use App\Models\SampahDimanfaatkan;
use App\Models\TempatTimbulanSampah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDistribusiSampahDimanfaatkanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user()->role != 'oss' && $this->user()->role != 'oks') {
            return false;
        }
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
            'sampah_dimanfaatkan_id' => [
                'required',
                'string',
                'exists:' . SampahDimanfaatkan::class . ',id', // Ensure the ID exists
            ],
            'jumlah_produk' => 'required|numeric',
            'tts_distribusi_id' => [
                'required',
                'string',
                'exists:' . TempatTimbulanSampah::class . ',id', // Ensure the ID exists
            ],
            'alamat_distribusi' => 'required|string',
            'link_online_distribusi' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID Produk Sampah Dimanfaatkan wajib diisi',
            'id.string' => 'ID Produk Sampah Dimanfaatkan harus berupa string',
            'id.exists' => 'ID Produk Sampah Dimanfaatkan tidak valid',
            'jumlah_produk.required' => 'Jumlah produk wajib diisi',
            'jumlah_produk.numeric' => 'Jumlah produk harus berupa angka',
            'tts_distribusi_id.required' => 'ID TKS Tempat Distribusi wajib diisi',
            'tts_distribusi_id.string' => 'ID TKS Tempat Distribusi harus berupa string',
            'tts_distribusi_id.exists' => 'ID TKS Tempat Distribusi tidak valid',
            'alamat_distribusi.required' => 'Alamat distribusi wajib diisi',
            'alamat_distribusi.string' => 'Alamat distribusi harus berupa string',
            'link_online_distribusi.required' => 'Link online distribusi wajib diisi',
            'link_online_distribusi.string' => 'Link online distribusi harus berupa string',
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
