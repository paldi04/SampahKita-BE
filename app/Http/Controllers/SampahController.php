<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampahDimanfaatkan\GetDistribusiSampahDimanfaatkanListRequest;
use App\Http\Requests\SampahDimanfaatkan\GetSampahDimanfaatkanDetailRequest;
use App\Http\Requests\SampahDimanfaatkan\GetSampahDimanfaatkanListRequest;
use App\Http\Requests\SampahDimanfaatkan\StoreDistribusiSampahDimanfaatkanRequest;
use App\Http\Requests\SampahDimanfaatkan\StoreSampahDimanfaatkanRequest;
use App\Http\Requests\SampahDimanfaatkan\UpdateSampahDimanfaatkanRequest;
use App\Http\Requests\SampahDiolah\StoreSampahDiolahRequest;
use App\Http\Requests\SampahDiolah\GetSampahDiolahDetailRequest;
use App\Http\Requests\SampahDiolah\GetSampahDiolahListRequest;
use App\Http\Requests\SampahDiolah\UpdateSampahDiolahRequest;
use App\Http\Requests\SampahMasuk\GetSampahKategoriListRequest;
use App\Http\Requests\SampahMasuk\GetSampahMasukDetailRequest;
use App\Http\Requests\SampahMasuk\GetSampahMasukListRequest;
use App\Http\Requests\SampahMasuk\GetSampahMasukStatusRequest;
use App\Http\Requests\SampahMasuk\StoreSampahMasukRequest;
use App\Http\Requests\SampahMasuk\UpdateSampahMasukRequest;
use App\Models\DistribusiSampahDimanfaatkan;
use App\Models\SampahDimanfaatkan;
use App\Models\SampahDiolah;
use App\Models\SampahKategori;
use App\Models\SampahMasuk;
use Carbon\Carbon;
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

        $list = SampahKategori::select('id', 'nama')->offset($offset)->limit($size)->get();

        $total = SampahKategori::count();

        $result = [
            'list' => $list,
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
                Storage::delete($sampahMasuk->foto_sampah);
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

        $list = SampahMasuk::select('id', 'tts_id', 'sampah_kategori_id', 'waktu_masuk', 'berat_kg', 'created_by')
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
            ->orderBy('updated_at', 'desc')
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
            'list' => $list,
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
                Storage::delete($sampahMasuk->foto_sampah);
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

    public function getSampahMasukStatus (GetSampahMasukStatusRequest $request)
    {
        $sampahMasuk = SampahMasuk::select('tts_id', 'sampah_kategori_id', DB::raw('SUM(berat_kg) as berat_kg'), DB::raw('MAX(updated_at) as last_updated_at'))
            ->when($request->tts_id, function ($query) use ($request) {
                $query->where('tts_id', '=', $request->tts_id);
            })
            ->when($request->sampah_kategori_id, function ($query) use ($request) {
                $query->where('sampah_kategori_id', '=', $request->sampah_kategori_id);
            })
            ->groupBy('tts_id', 'sampah_kategori_id')
            ->with('tempatTimbulanSampah:id,nama_tempat', 'sampahKategori:id,nama')
            ->get();

        $sampahMasuk = $sampahMasuk->map(function ($sampah) {
            $sampah->last_updated_at = Carbon::parse($sampah->last_updated_at)->format('Y-m-d H:i:s');
            
            $sampahDiolah = SampahDiolah::select('tss_id', 'sampah_kategori_id', 'status', 'berat_kg')
                ->where('tss_id', '=', $sampah->tts_id)
                ->where('sampah_kategori_id', '=', $sampah->sampah_kategori_id)
                ->where('status', '!=', 'dibatalkan')
                ->orderBy('updated_at', 'desc')
                ->get();

            $sampah->berat_kg -= $sampahDiolah->sum('berat_kg');
            $sampah->berat_kg = round($sampah->berat_kg, 2);
            $sampah->status = 'belum_diolah';
            return $sampah;
        });
        $sampahMasuk = $sampahMasuk->filter(function ($sampah) {
            return $sampah->berat_kg > 0;
        });
        $result = [
            'total_berat_kg' => round($sampahMasuk->sum('berat_kg'), 2),
            'list' => $sampahMasuk,
        ];
        
        return $this->sendResponse($result);
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
        $sampahDiolah = SampahDiolah::with('tempatSumberSampah:id,nama_tempat', 'sampahKategori:id,nama', 'tempatKumpulanSampah:id,nama_tempat', 'createdBy:id,nama', 'updatedBy:id,nama')
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

    public function getSampahDiolahStatus (GetSampahMasukStatusRequest $request)
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

            $uploadPath = 'tempat-timbunan-sampah/' . $sampahDimanfaatkan->tts_id . '/foto-produk';
            $uploadResult = uploadBase64Image($request->foto_produk, $uploadPath) ;
            if (!$uploadResult['url']) {
                DB::rollBack();
                return $this->sendError($uploadResult['error']);
            }
            $sampahDimanfaatkan->foto_produk = $uploadResult['url'];

            $sampahDimanfaatkan->kode_produk = $request->kode_produk;
            $result = $sampahDimanfaatkan->save();
            if (!$result) {
                Storage::delete($sampahDimanfaatkan->foto_sampah);
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
                $uploadPath = 'tempat-timbunan-sampah/' . $sampahDimanfaatkan->tts_id . '/foto-produk';
                $uploadResult = uploadBase64Image($request->foto_produk, $uploadPath) ;
                if (!$uploadResult['url']) {
                    DB::rollBack();
                    return $this->sendError($uploadResult['error']);
                }
                $sampahDimanfaatkan->foto_produk = $uploadResult['url'];
            }
            $sampahDimanfaatkan->kode_produk = $request->kode_produk ?? $sampahDimanfaatkan->kode_produk;
            $result = $sampahDimanfaatkan->save();
            if (!$result) {
                Storage::delete($sampahDimanfaatkan->foto_sampah);
                DB::rollBack();
                return $this->sendError('Update data sampah dimanfaatkan gagal, silahkan coba beberapa lagi!');
            }
            if ($request->foto_produk && $old_foto_produk) {
                Storage::delete($old_foto_produk);
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
