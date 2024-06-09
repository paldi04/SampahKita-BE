<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampahDimanfaatkan\GetDistribusiSampahDimanfaatkanListRequest;
use App\Http\Requests\SampahDimanfaatkan\GetSampahDimanfaatkanDetailRequest;
use App\Http\Requests\SampahDimanfaatkan\GetSampahDimanfaatkanListRequest;
use App\Http\Requests\SampahDimanfaatkan\StoreDistribusiSampahDimanfaatkanRequest;
use App\Http\Requests\SampahDimanfaatkan\StoreSampahDimanfaatkanRequest;
use App\Http\Requests\SampahDimanfaatkan\UpdateSampahDimanfaatkanRequest;
use App\Models\DistribusiSampahDimanfaatkan;
use App\Models\SampahDimanfaatkan;
use Illuminate\Support\Facades\DB;
use App\Helpers\CloudStorage as CloudStorage;

class SampahDimanfaatkanController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function storeSampahDimanfaatkan (StoreSampahDimanfaatkanRequest $request)
    {
        DB::beginTransaction();
        try {
            $sampahDimanfaatkan = new SampahDimanfaatkan();
            $sampahDimanfaatkan->tts_id = $request->tts_id;
            $sampahDimanfaatkan->sampah_kategori_id = $request->sampah_kategori_id;
            $sampahDimanfaatkan->berat_kg = $request->berat_kg;
            $sampahDimanfaatkan->nama_produk = $request->nama_produk;
            $sampahDimanfaatkan->nilai_jual = $request->nilai_jual;
            $sampahDimanfaatkan->jumlah_produk = $request->jumlah_produk;
            $sampahDimanfaatkan->kategori_produk = $request->kategori_produk;

            $uploadPath = 'tts/' . $sampahDimanfaatkan->tts_id . '/foto-produk';
            $uploadResult = CloudStorage::uploadBase64Image($request->foto_produk, $uploadPath) ;
            if (!$uploadResult['url']) {
                DB::rollBack();
                return $this->sendError($uploadResult['error']);
            }
            $sampahDimanfaatkan->foto_produk = $uploadResult['url'];

            $sampahDimanfaatkan->kode_produk = $request->kode_produk;
            $result = $sampahDimanfaatkan->save();
            if (!$result) {
                CloudStorage::delete($sampahDimanfaatkan->foto_sampah);
                DB::rollBack();
                return $this->sendError('Tambah data sampah dimanfaatkan gagal, silahkan coba beberapa lagi!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to store sampah dimanfaatkan', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($sampahDimanfaatkan);
    }
    
    public function getSampahDimanfaatkanList (GetSampahDimanfaatkanListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $list = SampahDimanfaatkan::select('id', 'tts_id', 'sampah_kategori_id', 'nama_produk', 'nilai_jual', 'foto_produk', 'kode_produk')
            ->with('tempatTimbulanSmpah:id,nama_tempat', 'sampahKategori:id,nama')
            ->where('tts_id', '=', $request->tts_id)
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
            })
            ->orderBy('updated_at', 'desc')
            ->offset($offset)->limit($size)->get();

        $total = SampahDimanfaatkan::where('tts_id', '=', $request->tts_id)
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
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

    public function getSampahDimanfaatkanDetail (GetSampahDimanfaatkanDetailRequest $request)
    {
        $sampahDimanfaatkan = SampahDimanfaatkan::with('tempatTimbulanSmpah:id,nama_tempat', 'sampahKategori:id,nama', 'createdBy:id,nama', 'updatedBy:id,nama')
            ->where('id', '=', $request->id)
            ->when($request->tts_id, function ($query) use ($request) {
                $query->where('tts_id', '=', $request->tts_id);
            })
            ->first();
        if (!$sampahDimanfaatkan) {
            return $this->sendError('Sampah dimanfaatkan tidak ditemukan!', [], 404);
        }
        $sampahDimanfaatkan->jumlah_produk_terdistribusi = DistribusiSampahDimanfaatkan::where('sampah_dimanfaatkan_id', '=', $sampahDimanfaatkan->id)->sum('jumlah_produk');
        return $this->sendResponse($sampahDimanfaatkan);
    }

    public function updateSampahDimanfaatkan (UpdateSampahDimanfaatkanRequest $request)
    {
        DB::beginTransaction();
        try {
            $sampahDimanfaatkan = SampahDimanfaatkan::where('id', $request->id)->where('tts_id', $request->tts_id)->first();
            if (!$sampahDimanfaatkan) {
                return $this->sendError('Sampah dimanfaatkan tidak ditemukan!', [], 404);
            }
            $sampahDimanfaatkan->berat_kg = $request->berat_kg ?? $sampahDimanfaatkan->berat_kg;
            $sampahDimanfaatkan->sampah_kategori_id = $request->sampah_kategori_id ?? $sampahDimanfaatkan->sampah_kategori_id;
            $sampahDimanfaatkan->nama_produk = $request->nama_produk ?? $sampahDimanfaatkan->nama_produk;
            $sampahDimanfaatkan->nilai_jual = $request->nilai_jual ?? $sampahDimanfaatkan->nilai_jual;
            $sampahDimanfaatkan->jumlah_produk = $request->jumlah_produk ?? $sampahDimanfaatkan->jumlah_produk;
            $sampahDimanfaatkan->kategori_produk = $request->kategori_produk ?? $sampahDimanfaatkan->kategori_produk;
            if ($request->foto_produk) {
                $old_foto_produk = $sampahDimanfaatkan->foto_produk;
                $uploadPath = 'tts/' . $sampahDimanfaatkan->tts_id . '/foto-produk';
                $uploadResult = CloudStorage::uploadBase64Image($request->foto_produk, $uploadPath) ;
                if (!$uploadResult['url']) {
                    DB::rollBack();
                    return $this->sendError($uploadResult['error']);
                }
                $sampahDimanfaatkan->foto_produk = $uploadResult['url'];
            }
            $sampahDimanfaatkan->kode_produk = $request->kode_produk ?? $sampahDimanfaatkan->kode_produk;
            $result = $sampahDimanfaatkan->save();
            if (!$result) {
                CloudStorage::delete($sampahDimanfaatkan->foto_sampah);
                DB::rollBack();
                return $this->sendError('Update data sampah dimanfaatkan gagal, silahkan coba beberapa lagi!');
            }
            if ($request->foto_produk && $old_foto_produk) {
                CloudStorage::delete($old_foto_produk);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to update sampah dimanfaatkan', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($sampahDimanfaatkan);
    }

    public function storeDistribusiSampahDimanfaatkan (StoreDistribusiSampahDimanfaatkanRequest $request)
    {
        DB::beginTransaction();
        try {
            $jumlahProduk = SampahDimanfaatkan::where('id', $request->sampah_dimanfaatkan_id)->value('jumlah_produk');
            $jumlahProdukTerdistibusi = DistribusiSampahDimanfaatkan::where('sampah_dimanfaatkan_id', $request->sampah_dimanfaatkan_id)->sum('jumlah_produk');
            $sisaProduk = $jumlahProduk - $jumlahProdukTerdistibusi;
            if ($request->jumlah_produk > $sisaProduk) {
                return $this->sendError('Jumlah produk yang akan didistribusikan melebihi sisa produk yang ada!', [], 400);
            }
            $distribusiSampahDimanfaatkan = new DistribusiSampahDimanfaatkan();
            $distribusiSampahDimanfaatkan->sampah_dimanfaatkan_id = $request->sampah_dimanfaatkan_id;
            $distribusiSampahDimanfaatkan->jumlah_produk = $request->jumlah_produk;
            $distribusiSampahDimanfaatkan->tts_distribusi_id = $request->tts_distribusi_id;
            $distribusiSampahDimanfaatkan->alamat_distribusi = $request->alamat_distribusi;
            $distribusiSampahDimanfaatkan->link_online_distribusi = $request->link_online_distribusi;
            $result = $distribusiSampahDimanfaatkan->save();
            if (!$result) {
                DB::rollBack();
                return $this->sendError('Distribusi sampah dimanfaatkan gagal, silahkan coba beberapa lagi!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to distribute sampah dimanfaatkan', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($distribusiSampahDimanfaatkan);
    }

    public function getDistribusiSampahDimanfaatkanList (GetDistribusiSampahDimanfaatkanListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $list = DistribusiSampahDimanfaatkan::select('id', 'sampah_dimanfaatkan_id', 'jumlah_produk', 'tts_distribusi_id', 'alamat_distribusi', 'link_online_distribusi')
            ->with('sampahDimanfaatkan:id,nama_produk', 'tempatTimbulanSampah:id,nama_tempat')
            ->where('sampah_dimanfaatkan_id', '=', $request->sampah_dimanfaatkan_id)
            ->orderBy('updated_at', 'desc')
            ->offset($offset)->limit($size)->get();

        $total = DistribusiSampahDimanfaatkan::where('sampah_dimanfaatkan_id', '=', $request->sampah_dimanfaatkan_id)
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
}
