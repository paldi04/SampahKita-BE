<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SampahDimanfaatkan extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tts_id',
        'sampah_kategori_id',
        'berat_kg',
        'nama_produk',
        'nilai_jual',
        'jumlah_produk',
        'kategori_produk',
        'foto_produk',
        'kode_produk',
        'created_by',
        'updated_by',
    ];

    public function tempatTimbulanSmpah()
    {
        return $this->belongsTo(TempatTimbulanSampah::class, 'tts_id', 'id');
    }

    public function sampahKategori()
    {
        return $this->belongsTo(SampahKategori::class, 'sampah_kategori_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
