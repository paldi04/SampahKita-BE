<?php

namespace App\Http\Controllers;

use App\Http\Requests\TempatTimbulanSampah\GetTempatTimbulanSampahDetailRequest;
use App\Http\Requests\TempatTimbulanSampah\GetTempatTimbulanSampahKategoriListRequest;
use App\Http\Requests\TempatTimbulanSampah\GetTempatTimbulanSampahSektorListRequest;
use App\Http\Requests\TempatTimbulanSampah\GetTempatTimbulanSampahListRequest;
use App\Models\TempatTimbulanSampah;
use App\Http\Requests\TempatTimbulanSampah\UpdateTempatTimbulanSampahRequest;
use App\Models\TempatTimbulanSampahKategori;
use App\Models\TempatTimbulanSampahSektor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TempatTimbulanSampahController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['getTempatTimbulanSampahKategoriList', 'getTempatTimbulanSampahSektorList']);
    }

    public function getTempatTimbulanSampahKategoriList(GetTempatTimbulanSampahKategoriListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $users = TempatTimbulanSampahKategori::select('id', 'name')->offset($offset)->limit($size)->get();

        $total = TempatTimbulanSampahKategori::count();

        $result = [
            'list' => $users,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }

    public function getTempatTimbulanSampahSektorList(GetTempatTimbulanSampahSektorListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $users = TempatTimbulanSampahSektor::select('id', 'name')
            ->where('tts_kategori_id', '=', $request->tts_kategori_id)
            ->offset($offset)->limit($size)->get();

        $total = TempatTimbulanSampahSektor::where('tts_kategori_id', '=', $request->tts_kategori_id)->count();

        $result = [
            'list' => $users,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }

    public function getTempatTimbulanSampahList(GetTempatTimbulanSampahListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $users = TempatTimbulanSampah::select('id', 'nama_tempat', 'tts_kategori_id', 'tts_sektor_id', 'alamat_tempat', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at')
            ->when($request->nama_tempat, function ($query) use ($request) {
                $query->where('nama_tempat', 'like', '%' . $request->nama_tempat . '%');
            })
            ->when($request->tts_kategori_id, function ($query) use ($request) {
                $query->where('tts_kategori_id', '=', $request->tts_kategori_id);
            })
            ->when($request->tts_sektor_id, function ($query) use ($request) {
                $query->where('tts_sektor_id', '=', $request->tts_sektor_id);
            })
            ->when($request->alamat_tempat, function ($query) use ($request) {
                $query->where('alamat_tempat', 'like', '%' . $request->alamat_tempat . '%');
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', 'like', '%' . $request->status . '%');
            })
            ->with(['tempatTimbulanSampahKategori:id,name', 'tempatTimbulanSampahSektor:id,name'])
            ->offset($offset)->limit($size)->get();

        $total = TempatTimbulanSampah::when($request->nama_tempat, function ($query) use ($request) {
            $query->where('nama_tempat', 'like', '%' . $request->nama_tempat . '%');
        })
            ->when($request->tts_kategori_id, function ($query) use ($request) {
                $query->where('tts_kategori_id', '=', $request->tts_kategori_id);
            })
            ->when($request->tts_sektor_id, function ($query) use ($request) {
                $query->where('tts_sektor_id', '=', $request->tts_sektor_id);
            })
            ->when($request->alamat_tempat, function ($query) use ($request) {
                $query->where('alamat_tempat', 'like', '%' . $request->alamat_tempat . '%');
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', 'like', '%' . $request->status . '%');
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

    public function getTempatTimbulanSampahDetail(GetTempatTimbulanSampahDetailRequest $request)
    {
        $withData = [
            'tempatTimbulanSampahKategori:id,name',
            'tempatTimbulanSampahSektor:id,name',
            'createdBy:id,name',
            'updatedBy:id,name',
            'user:id,user_role_id,name,email,phone_number,last_active_at,tts_id',
            'user.userRole:id,name'
        ];
        $tempatTimbulanSampah = TempatTimbulanSampah::where('id', '=', $request->id)->with($withData)->first();
        if (!$tempatTimbulanSampah) {
            return $this->sendError('Tempat Timbulan Sampah tidak ditemukan!', 404);
        }

        return $this->sendResponse($tempatTimbulanSampah);
    }

    public function updateTempatTimbulanSampah(UpdateTempatTimbulanSampahRequest $request)
    {
        $tempatTimbulanSampah = TempatTimbulanSampah::find($request->id);
        if (!$tempatTimbulanSampah) {
            return $this->sendError('Tempat Timbulan Sampah tidak ditemukan!', 404);
        }

        DB::beginTransaction();
        try {

            $tempatTimbulanSampah->nama_tempat = $request->nama_tempat ?? $tempatTimbulanSampah->nama_tempat;
            $tempatTimbulanSampah->alamat_tempat = $request->alamat_tempat ?? $tempatTimbulanSampah->alamat_tempat;
            $tempatTimbulanSampah->afiliasi = $request->afiliasi ?? $tempatTimbulanSampah->afiliasi;
            $tempatTimbulanSampah->latitude = $request->latitude ?? $tempatTimbulanSampah->latitude;
            $tempatTimbulanSampah->longitude = $request->longitude ?? $tempatTimbulanSampah->longitude;
            $tempatTimbulanSampah->luas_lahan = $request->luas_lahan ?? $tempatTimbulanSampah->luas_lahan;
            $tempatTimbulanSampah->luas_bangunan = $request->luas_bangunan ?? $tempatTimbulanSampah->luas_bangunan;
            $tempatTimbulanSampah->panjang = $request->panjang ?? $tempatTimbulanSampah->panjang;
            $tempatTimbulanSampah->lebar = $request->lebar ?? $tempatTimbulanSampah->lebar;
            $tempatTimbulanSampah->sisa_lahan = $request->sisa_lahan ?? $tempatTimbulanSampah->sisa_lahan;
            $tempatTimbulanSampah->kepemilikan_lahan = $request->kepemilikan_lahan ?? $tempatTimbulanSampah->kepemilikan_lahan;
            $tempatTimbulanSampah->status = $request->status ?? $tempatTimbulanSampah->status;
            $tempatTimbulanSampah->updated_by = auth()->user()->id;
    
            if ($request->foto_tempat) {
                $old_foto_tempat = $tempatTimbulanSampah->foto_tempat;
                $foto_tempat = [];
                for ($i = 0; $i < count($request->foto_tempat); $i++) {
                    $uploadPath = 'tempat-timbunan-sampah/' . $tempatTimbulanSampah->id . '/foto-tempat';
                    $uploadResult = uploadBase64Image($request->foto_tempat[$i], $uploadPath) ;
                    if (!$uploadResult['url']) {
                        for ($j = 0; $j < $i; $j++) {
                            Storage::delete($foto_tempat);
                        }
                        DB::rollBack();
                        return $this->sendError($uploadResult['error']);
                    }
                    $foto_tempat[] = $uploadResult['url'];
                }
                $tempatTimbulanSampah->foto_tempat = $foto_tempat;
            }
    
            $result = $tempatTimbulanSampah->save();
            if (!$result) {
                DB::rollBack();
                return $this->sendError('Update Tempat Timbulan Sampah gagal, silahkan coba kembali beberapa saat lagi!');
            }
            Storage::delete($old_foto_tempat);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Update Tempat Timbulan Sampah gagal, silahkan coba kembali beberapa saat lagi!', [ "error" => $e->getMessage() ]);
            return $this->sendError('User registration failed', [ "error" => $e->getMessage() ]);
        }
        DB::commit();
        return $this->sendResponse($tempatTimbulanSampah);
    }
}
