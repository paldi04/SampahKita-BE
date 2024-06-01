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
        'afiliasi',
        'alamat_provinsi',
        'alamat_kota',
        'alamat_rw',
        'alamat_rt',
        'alamat_lengkap',
        'alamat_latitude',
        'alamat_longitude',
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
    public function user()
    {
        return $this->hasMany(User::class, 'tts_id');
    }

    public function sampahMasuks()
    {
        return $this->hasMany(SampahMasuk::class, 'tts_id');
    }

    public function sampahDiolahs()
    {
        return $this->hasMany(SampahDiolah::class, 'tss_id');
    }

    public function sampahDiolahOlehs()
    {
        return $this->hasMany(SampahDiolah::class, 'tks_id');
    }

    public function sampahDimanfaatkan()
    {
        return $this->hasMany(SampahDimanfaatkan::class, 'tts_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

}
