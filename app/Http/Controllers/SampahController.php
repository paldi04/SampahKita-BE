<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sampah\GetSampahKategoriListRequest;
use App\Http\Requests\Sampah\GetSampahMasukListRequest;
use App\Http\Requests\Sampah\StoreSampahMasukRequest;
use App\Models\SampahKategori;
use App\Models\SampahMasuk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SampahController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['getSampahKategoriList']);
    }
    public function getSampahKategoriList(GetSampahKategoriListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $users = SampahKategori::select('id', 'nama')->offset($offset)->limit($size)->get();

        $total = SampahKategori::count();

        $result = [
            'list' => $users,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }

    public function storeSampahMasuk(StoreSampahMasukRequest $request)
    {
        DB::beginTransaction();
        try {
            $sampahMasuk = new SampahMasuk();
            $sampahMasuk->tts_id = $request->tts_id;
            $sampahMasuk->sampah_kategori_id = $request->sampah_kategori_id;

            $uploadPath = 'tempat-timbunan-sampah/' . $sampahMasuk->tts_id . '/foto-sampah-masuk';
            $uploadResult = uploadBase64Image($request->foto_sampah, $uploadPath) ;
            if (!$uploadResult['url']) {
                DB::rollBack();
                return $this->sendError($uploadResult['error']);
            }
            $sampahMasuk->foto_sampah = $uploadResult['url'];

            $sampahMasuk->waktu_masuk = $request->waktu_masuk;
            $sampahMasuk->berat_kg = $request->berat_kg;
            $sampahMasuk->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to store sampah masuk', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($sampahMasuk);
    }

    public function getSampahMasukList(GetSampahMasukListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $users = SampahMasuk::offset($offset)->limit($size)->get();

        $total = SampahMasuk::count();

        $result = [
            'list' => $users,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }
}
