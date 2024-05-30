<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SampahDiolah extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tts_id',
        'sampah_kategori_id',
        'berat_kg',
        'diolah_oleh',
        'tks_id',
        'waktu_diolah',
        'status',
        'created_by',
        'updated_by',
    ];

    public function tempatSumberSampah()
    {
        return $this->belongsTo(TempatTimbulanSampah::class, 'tss_id', 'id');
    }

    public function sampahKategori()
    {
        return $this->belongsTo(SampahKategori::class, 'sampah_kategori_id', 'id');
    }

    public function tempatKumpulanSampah()
    {
        return $this->belongsTo(TempatTimbulanSampah::class, 'tks_id', 'id');
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
