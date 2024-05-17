<?php

namespace App\Http\Requests\Auth;

use App\Models\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->merge(['user_role_id' => $this->route('user_role_id')]);
        $tempat_timbulan_sampah = $this->input('tempat_timbulan_sampah');
        $tempat_timbulan_sampah['tts_kategori_id'] = $this->route('tts_kategori_id');
        $this->merge(['tempat_timbulan_sampah' => $tempat_timbulan_sampah]);
        return $this->input('user_role_id') !== 'admin';
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
            'user_role_id' => [
                    'required',
                    'string',
                    'exists:user_roles,id', // Ensure the ID exists in the user_roles table
            ],
            'name' => 'string|max:255',
            'phone_number' => 'required|string|regex:/^[0-9]{7,15}$/|starts_with:08|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])[\s\S]{4,}$/',
            'tempat_timbulan_sampah.nama_tempat' => 'required|string|max:255',
            'tempat_timbulan_sampah.tts_kategori_id' => 'required|string|exists:tempat_timbulan_sampah_kategoris,id',
            'tempat_timbulan_sampah.tts_sektor_id' => 'nullable|exists:tempat_timbulan_sampah_sektors,id',
            'tempat_timbulan_sampah.alamat_tempat' => 'required|string|max:255',
            'tempat_timbulan_sampah.afiliasi' => 'nullable|string|max:255',
            'tempat_timbulan_sampah.latitude' => 'required|string',
            'tempat_timbulan_sampah.longitude' => 'required|string',
            'tempat_timbulan_sampah.luas_lahan' => 'required|numeric',
            'tempat_timbulan_sampah.luas_bangunan' => 'required|numeric',
            'tempat_timbulan_sampah.panjang' => 'required|numeric',
            'tempat_timbulan_sampah.lebar' => 'required|numeric',
            'tempat_timbulan_sampah.sisa_lahan' => 'required|numeric',
            'tempat_timbulan_sampah.kepemilikan_lahan' => 'required|string|max:255',
            'tempat_timbulan_sampah.foto_tempat' => 'nullable|array',
            'tempat_timbulan_sampah.foto_tempat.*' => 'nullable|string',
            'tempat_timbulan_sampah.status' => 'required|string|in:active,inactive'
        ];
    }

    public function messages(): array
    {
        return [
            'user_role_id.exists' => 'ID role pengguna tidak valid!',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter!',
            'phone_number.regex' => 'Format nomor telepon tidak valid!',
            'phone_number.starts_with' => 'Nomor telepon harus diawali dengan 08!',
            'username.regex' => 'Format username harus alfanumerik dan underscore saja!',
            'password.regex' => 'Format password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial!',
            'tempat_timbulan_sampah.tts_kategori_id.exists' => 'ID kategori tempat timbulan sampah tidak valid!',
            'tempat_timbulan_sampah.tts_sektor_id.exists' => 'ID sektor tempat timbulan sampah tidak valid!',
            'tempat_timbulan_sampah.luas_lahan.numeric' => 'Luas lahan harus berupa angka!',
            'tempat_timbulan_sampah.luas_bangunan.numeric' => 'Luas bangunan harus berupa angka!',
            'tempat_timbulan_sampah.panjang.numeric' => 'Panjang harus berupa angka!',
            'tempat_timbulan_sampah.lebar.numeric' => 'Lebar harus berupa angka!',
            'tempat_timbulan_sampah.sisa_lahan.numeric' => 'Sisa lahan harus berupa angka!',
            'tempat_timbulan_sampah.foto_tempat.array' => 'Foto tempat harus berupa array!',
            'tempat_timbulan_sampah.status.string' => 'Foto tempat harus berupa string!',
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
