<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SampahMasuk extends Model
{
    public $incrementing = false;
    
    protected $fillable = [
        'tts_id',
        'sampah_kategori_id',
        'foto_timbangan',
        'total_kg',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_kg' => 'decimal:2',
        'waktu_masuk' => 'datetime:Y-m-d H:i:s',
        'id' => 'string',
    ];

    public function tempatTimbulanSampah()
    {
        return $this->belongsTo(TempatTimbulanSampah::class, 'tts_id');
    }
    
    public function sampahKategori()
    {
        return $this->belongsTo(SampahKategori::class);
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
