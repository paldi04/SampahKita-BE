<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempatTimbulanSampah extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'nama_tempat',
        'tts_kategori_id',
        'tts_sektor_id',
        'alamat_tempat',
        'afiliasi',
        'latitude',
        'longitude',
        'luas_lahan',
        'luas_bangunan',
        'panjang',
        'lebar',
        'sisa_lahan',
        'kepemilikan_lahan',
        'foto_tempat',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'foto_tempat' => 'array',
    ];

    public function tempatTimbulanSampahKategori()
    {
        return $this->belongsTo(TempatTimbulanSampahKategori::class, 'tts_kategori_id');
    }

    public function tempatTimbulanSampahSektor()
    {
        return $this->belongsTo(TempatTimbulanSampahSektor::class, 'tts_sektor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

}
