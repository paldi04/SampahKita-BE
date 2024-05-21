<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterTempatTimbulanSampahRequest;
use App\Models\TempatTimbulanSampah;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registerTempatTimbulanSampah']]);
    }

    public function registerTempatTimbulanSampah(RegisterTempatTimbulanSampahRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = new User();
            $user->id = Str::uuid()->toString();
            $user->nama = 'Operator ' . $request->tempat_timbulan_sampah['nama_tempat'];
            $user->user_role_id = $request->tempat_timbulan_sampah['tts_kategori_id'] == 'tss' ? 'oss' : 'oks';
            $user->nomor_telepon = $request->user['nomor_telepon'];
            $user->email = $request->user['email'];
            $user->password = Hash::make($request->user['password']);
            $isCreated = $user->save();
            if (!$isCreated) {
                DB::rollBack();
                return $this->sendError('Pembuatan akun gagal, silahkan coba kembali beberapa saat lagi!');
            }
            $tempatTimbulanSampah = new TempatTimbulanSampah();
            $tempatTimbulanSampah->id = Str::uuid()->toString();
            $tempatTimbulanSampah->nama_tempat = $request->tempat_timbulan_sampah['nama_tempat'];
            $tempatTimbulanSampah->tts_kategori_id = $request->tempat_timbulan_sampah['tts_kategori_id'];
            $tempatTimbulanSampah->tts_sektor_id = $request->tempat_timbulan_sampah['tts_sektor_id'];
            $tempatTimbulanSampah->alamat_tempat = $request->tempat_timbulan_sampah['alamat_tempat'];
            $tempatTimbulanSampah->afiliasi = $request->tempat_timbulan_sampah['afiliasi'];
            $tempatTimbulanSampah->latitude = $request->tempat_timbulan_sampah['latitude'];
            $tempatTimbulanSampah->longitude = $request->tempat_timbulan_sampah['longitude'];
            $tempatTimbulanSampah->luas_lahan = $request->tempat_timbulan_sampah['luas_lahan'];
            $tempatTimbulanSampah->luas_bangunan = $request->tempat_timbulan_sampah['luas_bangunan'];
            $tempatTimbulanSampah->panjang = $request->tempat_timbulan_sampah['panjang'];
            $tempatTimbulanSampah->lebar = $request->tempat_timbulan_sampah['lebar'];
            $tempatTimbulanSampah->sisa_lahan = $request->tempat_timbulan_sampah['sisa_lahan'];
            $tempatTimbulanSampah->kepemilikan_lahan = $request->tempat_timbulan_sampah['kepemilikan_lahan'];
            $tempatTimbulanSampah->status = $request->tempat_timbulan_sampah['status'];
            $foto_tempat = [];
            for ($i = 0; $i < count($request->tempat_timbulan_sampah['foto_tempat']); $i++) {
                $uploadPath = 'tempat-timbunan-sampah/' . $tempatTimbulanSampah->id . '/foto-tempat';
                $uploadResult = uploadBase64Image($request->tempat_timbulan_sampah['foto_tempat'][$i], $uploadPath) ;
                if (!$uploadResult['url']) {
                    for ($j = 0; $j < $i; $j++) {
                        unlink($foto_tempat[$j]);
                    }
                    DB::rollBack();
                    return $this->sendError($uploadResult['error']);
                }
                $foto_tempat[] = $uploadResult['url'];
            }
            $tempatTimbulanSampah->foto_tempat = $foto_tempat;
            $tempatTimbulanSampah->created_by = $user->id ;
            $tempatTimbulanSampah->updated_by = $user->id ;
            $isCreated = $tempatTimbulanSampah->save();
            if (!$isCreated) {
                for ($j = 0; $j < $i; $j++) {
                    unlink($foto_tempat[$j]);
                }
                DB::rollBack();
                return $this->sendError('Pembuatan tempat timbulan gagal, silahkan coba kembali beberapa saat lagi!');
            }
            $user->tts_id = $tempatTimbulanSampah->id;
            $isUpdated = $user->update();
            if (!$isUpdated) {
                for ($j = 0; $j < $i; $j++) {
                    unlink($foto_tempat[$j]);
                }
                DB::rollBack();
                return $this->sendError('Pembuatan akun gagal, silahkan coba kembali beberapa saat lagi!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('User registration failed', [ "error" => $e->getMessage() ]);
        }
        DB::commit();
        return $this->sendResponse([ 'id' => $user->id  ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'nomor_telepon';
        $credentials = array(
            $field => $request->username,
            'password' => $request->password
        );
        $token = auth()->attempt($credentials);
        
        if (!$token) {
            return $this->sendError('Unauthorized', [], 401);
        }

        return $this->sendResponse([
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Check token expiration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        return $this->sendResponse(auth()->payload());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return $this->sendResponse([], 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = auth()->refresh();
        if(!$token){
            return $this->sendError('Unauthorized', [], 401);
        }
        return $this->sendResponse([
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Change password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return $this->sendError('Old password did not match!', [], 400);
        }

        User::where('id', auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

        return $this->sendResponse([], 'Password has been changed');
    }
    
}