<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sampah\GetSampahKategoriListRequest;
use App\Http\Requests\Sampah\GetSampahMasukDetailRequest;
use App\Http\Requests\Sampah\GetSampahMasukListRequest;
use App\Http\Requests\Sampah\StoreSampahMasukRequest;
use App\Http\Requests\Sampah\UpdateSampahMasukRequest;
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
            $result = $sampahMasuk->save();
            if (!$result) {
                DB::rollBack();
                return $this->sendError('Tambah data sampah masuk gagal, silahkan coba beberapa lagi!');
            }
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

        $users = SampahMasuk::select('id', 'tts_id', 'sampah_kategori_id', 'waktu_masuk', 'berat_kg', 'created_by')
            ->with('tempatTimbulanSampah:id,nama_tempat', 'sampahKategori:id,nama', 'createdBy:id,nama')
            ->where('tts_id', '=', $request->tts_id)
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
            })
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('waktu_masuk', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('waktu_masuk', '<=', $request->end_date);
            })
            ->offset($offset)->limit($size)->get();

        $total = SampahMasuk::where('tts_id', '=', $request->tts_id)
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
            })
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('waktu_masuk', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('waktu_masuk', '<=', $request->end_date);
            })
            ->count();

        $result = [
            'list' => $users,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }

    public function getSampahMasukDetail(GetSampahMasukDetailRequest $request)
    {
        $sampahMasuk = SampahMasuk::with('tempatTimbulanSampah:id,nama_tempat', 'sampahKategori:id,nama', 'createdBy:id,nama', 'updatedBy:id,nama')
            ->where('id', '=', $request->id)
            ->where('tts_id', '=', $request->tts_id)
            ->first();
        if (!$sampahMasuk) {
            return $this->sendError('Sampah masuk tidak ditemukan!', [], 404);
        }
        return $this->sendResponse($sampahMasuk);
    }

    public function updateSampahMasuk(UpdateSampahMasukRequest $request)
    {
        DB::beginTransaction();
        try {
            $sampahMasuk = SampahMasuk::where('id', $request->id)->where('tts_id', $request->tts_id)->first();
            if (!$sampahMasuk) {
                return $this->sendError('Sampah masuk tidak ditemukan!', [], 404);
            }

            $sampahMasuk->sampah_kategori_id = $request->sampah_kategori_id ?? $sampahMasuk->sampah_kategori_id;
            if ($request->foto_sampah) {
                $old_foto_sampah = $sampahMasuk->foto_sampah;
                $uploadPath = 'tempat-timbunan-sampah/' . $sampahMasuk->tts_id . '/foto-sampah-masuk';
                $uploadResult = uploadBase64Image($request->foto_sampah, $uploadPath) ;
                if (!$uploadResult['url']) {
                    DB::rollBack();
                    return $this->sendError($uploadResult['error']);
                }
                $sampahMasuk->foto_sampah = $uploadResult['url'];
            }

            $sampahMasuk->waktu_masuk = $request->waktu_masuk;
            $sampahMasuk->berat_kg = $request->berat_kg;
            $result = $sampahMasuk->save();
            if (!$result) {
                DB::rollBack();
                return $this->sendError('Update data sampah masuk gagal, silahkan coba beberapa lagi!');
            }
            if ($request->foto_sampah && $old_foto_sampah) {
                Storage::delete($old_foto_sampah);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to update sampah masuk', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($sampahMasuk);
    }
}
