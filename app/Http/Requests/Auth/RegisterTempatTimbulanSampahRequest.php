<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterTempatTimbulanSampahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'user.nomor_telepon' => 'required|string|regex:/^[0-9]{7,15}$/|starts_with:08|unique:users,nomor_telepon',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|string|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])[\s\S]{6,}$/',
            'tempat_timbulan_sampah.nama_tempat' => 'required|string|max:255',
            'tempat_timbulan_sampah.tts_kategori_id' => 'required|string|exists:tempat_timbulan_sampah_kategoris,id',
            'tempat_timbulan_sampah.tts_sektor_id' => 'nullable|exists:tempat_timbulan_sampah_sektors,id',
            'tempat_timbulan_sampah.afiliasi' => 'nullable|string|max:255',
            'tempat_timbulan_sampah.alamat_provinsi' => 'required|string|max:255',
            'tempat_timbulan_sampah.alamat_kota' => 'required|string|max:255',
            'tempat_timbulan_sampah.alamat_rw' => 'required|string|max:255',
            'tempat_timbulan_sampah.alamat_rt' => 'required|string|max:255',
            'tempat_timbulan_sampah.alamat_lengkap' => 'required|string',
            'tempat_timbulan_sampah.alamat_latitude' => 'required|string',
            'tempat_timbulan_sampah.alamat_longitude' => 'required|string',
            'tempat_timbulan_sampah.luas_lahan' => 'required|numeric',
            'tempat_timbulan_sampah.luas_bangunan' => 'required|numeric',
            'tempat_timbulan_sampah.panjang' => 'required|numeric',
            'tempat_timbulan_sampah.lebar' => 'required|numeric',
            'tempat_timbulan_sampah.sisa_lahan' => 'required|numeric',
            'tempat_timbulan_sampah.kepemilikan_lahan' => 'required|string|max:255',
            'tempat_timbulan_sampah.foto_tempat' => 'nullable|array',
            'tempat_timbulan_sampah.foto_tempat.*' => 'nullable|string',
            'tempat_timbulan_sampah.status' => 'required|string|in:aktif,tidak_aktif'
        ];
    }

    public function messages(): array
    {
        return [
            'user.nama.max' => 'Nama tidak boleh lebih dari 255 karakter!',
            'user.nomor_telepon.regex' => 'Format nomor telepon tidak valid!',
            'user.nomor_telepon.starts_with' => 'Nomor telepon harus diawali dengan 08!',
            'user.username.regex' => 'Format username harus alfanumerik dan underscore saja!',
            'user.password.regex' => 'Format password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial!',
            'tempat_timbulan_sampah.tts_kategori_id.exists' => 'ID kategori tempat timbulan sampah tidak valid!',
            'tempat_timbulan_sampah.tts_sektor_id.exists' => 'ID sektor tempat timbulan sampah tidak valid!',
            'tempat_timbulan_sampah.luas_lahan.numeric' => 'Luas lahan harus berupa angka!',
            'tempat_timbulan_sampah.luas_bangunan.numeric' => 'Luas bangunan harus berupa angka!',
            'tempat_timbulan_sampah.panjang.numeric' => 'Panjang harus berupa angka!',
            'tempat_timbulan_sampah.lebar.numeric' => 'Lebar harus berupa angka!',
            'tempat_timbulan_sampah.sisa_lahan.numeric' => 'Sisa lahan harus berupa angka!',
            'tempat_timbulan_sampah.foto_tempat.array' => 'Foto tempat harus berupa array!',
            'tempat_timbulan_sampah.status.in' => 'Status harus berupa aktif atau tidak_aktif!'
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
