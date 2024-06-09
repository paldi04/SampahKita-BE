<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampahDiolah\StoreSampahDiolahRequest;
use App\Http\Requests\SampahDiolah\GetSampahDiolahDetailRequest;
use App\Http\Requests\SampahDiolah\GetSampahDiolahListRequest;
use App\Http\Requests\SampahDiolah\UpdateSampahDiolahRequest;
use App\Http\Requests\SampahDiolah\GetSampahDiolahStatusRequest;
use App\Models\SampahDiolah;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ballen\Distical\Calculator as DistanceCalculator;
use Ballen\Distical\Entities\LatLong;

class SampahDiolahController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function storeSampahDiolah (StoreSampahDiolahRequest $request)
    {
        DB::beginTransaction();
        try {
            $sampahDiolah = new SampahDiolah();
            $sampahDiolah->tss_id = $request->tss_id;
            $sampahDiolah->sampah_kategori_id = $request->sampah_kategori_id;
            $sampahDiolah->berat_kg = $request->berat_kg;
            $sampahDiolah->diolah_oleh = $request->diolah_oleh;
            if ($request->diolah_oleh === 'tks') {
                $sampahDiolah->tks_id = $request->tks_id;
                $sampahDiolah->status = 'menunggu_respon';
            } else {
                $sampahDiolah->status = 'sudah_direspon';
            }
            $sampahDiolah->waktu_diolah = $request->waktu_diolah;
            $result = $sampahDiolah->save();
            if (!$result) {
                DB::rollBack();
                return $this->sendError('Tambah data sampah diolah gagal, silahkan coba beberapa lagi!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to store sampah diolah', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($sampahDiolah);
    }

    public function getSampahDiolahList (GetSampahDiolahListRequest $request) 
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $list = SampahDiolah::select('id', 'tss_id', 'sampah_kategori_id', 'berat_kg', 'diolah_oleh', 'tks_id', 'waktu_diolah', 'status', 'created_by')
            ->with('tempatSumberSampah:id,nama_tempat', 'sampahKategori:id,nama', 'tempatKumpulanSampah:id,nama_tempat', 'createdBy:id,nama')
            ->where('tss_id', '=', $request->tss_id)
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
            })
            ->when($request->diolah_oleh, function ($query) use ($request) {
                $query->where('diolah_oleh', '=', $request->diolah_oleh);
            })
            ->when($request->tks_id, function ($query) use ($request) {
                $query->where('tks_id', '=', $request->tks_id);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', '=', $request->status);
            })
            ->orderBy('updated_at', 'desc')
            ->offset($offset)->limit($size)->get();

        $total = SampahDiolah::where('tss_id', '=', $request->tss_id)
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
            })
            ->when($request->diolah_oleh, function ($query) use ($request) {
                $query->where('diolah_oleh', '=', $request->diolah_oleh);
            })
            ->when($request->tks_id, function ($query) use ($request) {
                $query->where('tks_id', '=', $request->tks_id);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', '=', $request->status);
            })
            ->count();

        $result = [
            'list' => $list,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }        

    public function getSampahDiolahDetail (GetSampahDiolahDetailRequest $request)
    {
        $sampahDiolah = SampahDiolah::with('tempatSumberSampah:id,nama_tempat,alamat_lengkap,alamat_latitude,alamat_longitude', 'sampahKategori:id,nama', 'tempatKumpulanSampah:id,nama_tempat,alamat_lengkap,alamat_latitude,alamat_longitude', 'createdBy:id,nama', 'updatedBy:id,nama')
            ->where('id', '=', $request->id)
            ->when($request->tss_id, function ($query) use ($request) {
                $query->where('tss_id', '=', $request->tss_id);
            })
            ->when($request->tks_id, function ($query) use ($request) {
                $query->where('tks_id', '=', $request->tks_id);
            })
            ->first();
        if (!$sampahDiolah) {
            return $this->sendError('Sampah diolah tidak ditemukan!', [], 404);
        }

        try {
            $firstLocation = new LatLong($sampahDiolah->tempatSumberSampah->alamat_latitude, $sampahDiolah->tempatSumberSampah->alamat_longitude);
            $secondLocation = new LatLong($sampahDiolah->tempatKumpulanSampah->alamat_latitude, $sampahDiolah->tempatKumpulanSampah->alamat_longitude);
            $distanceCalculator = new DistanceCalculator($firstLocation, $secondLocation);
            $distance = $distanceCalculator->get();
            $sampahDiolah->jarak_tempuh = round($distance->asKilometres(), 1).'km';
        } catch (\Exception $e) {
            $sampahDiolah->jarak_tempuh = '-';
        }

        return $this->sendResponse($sampahDiolah);
    }

    public function updateSampahDiolah (UpdateSampahDiolahRequest $request)
    {
        DB::beginTransaction();
        try {
            $sampahDiolah = SampahDiolah::where('id', $request->id)->where('tss_id', $request->tss_id)->first();
            if (!$sampahDiolah) {
                return $this->sendError('Sampah diolah tidak ditemukan!', [], 404);
            }
            $sampahDiolah->status = $request->status ?? $sampahDiolah->status;
            $sampahDiolah->keterangan = $request->keterangan ?? $sampahDiolah->keterangan;
            $result = $sampahDiolah->save();
            if (!$result) {
                DB::rollBack();
                return $this->sendError('Update data sampah diolah gagal, silahkan coba beberapa lagi!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to update sampah diolah', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($sampahDiolah);
    }

    public function getSampahDiolahStatus (GetSampahDiolahStatusRequest $request)
    {
        $sampahDiolah = SampahDiolah::select('tss_id', 'sampah_kategori_id', DB::raw('SUM(berat_kg) as berat_kg'), DB::raw('MAX(updated_at) as last_updated_at'))
            ->when($request->tss_id, function ($query) use ($request) {
                $query->where('tss_id', '=', $request->tss_id);
            })
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
            })
            ->groupBy('tss_id', 'sampah_kategori_id')
            ->with('tempatSumberSampah:id,nama_tempat', 'sampahKategori:id,nama')
            ->get();

        $sampahDiolah = $sampahDiolah->map(function ($sampah) {
            $sampah->last_updated_at = Carbon::parse($sampah->last_updated_at)->format('Y-m-d H:i:s');
            return $sampah;
        });
        $result = [
            'total_berat_kg' => round($sampahDiolah->sum('berat_kg'), 2),
            'list' => $sampahDiolah,
        ];
        
        return $this->sendResponse($result);
    }

}
