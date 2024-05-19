<?php

namespace App\Http\Controllers;

use App\Http\Requests\TempatTimbulanSampah\GetTempatTimbulanSampahKategoriListRequest;
use App\Http\Requests\TempatTimbulanSampah\GetTempatTimbulanSampahSektorListRequest;
use App\Http\Requests\TempatTimbulanSampah\GetTempatTimbulanSampahListRequest;
use App\Models\TempatTimbulanSampah;
use App\Http\Requests\TempatTimbulanSampah\StoreTempatTimbulanSampahRequest;
use App\Http\Requests\TempatTimbulanSampah\UpdateTempatTimbulanSampahRequest;
use App\Models\TempatTimbulanSampahKategori;
use App\Models\TempatTimbulanSampahSektor;

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
            ->with(['tempatTimbulanSampahKategori', 'tempatTimbulanSampahSektor'])
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
}
