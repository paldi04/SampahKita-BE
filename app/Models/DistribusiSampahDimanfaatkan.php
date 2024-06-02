<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DistribusiSampahDimanfaatkan extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'sampah_dimanfaatkan_id',
        'jumlah_produk',
        'tts_distribusi_id',
        'alamat_distribusi',
        'link_online_distribusi',
        'created_by',
        'updated_by'
    ];

    public function sampahDimanfaatkan()
    {
        return $this->belongsTo(SampahDimanfaatkan::class, 'sampah_dimanfaatkan_id', 'id');
    }

    public function tempatTimbulanSampah()
    {
        return $this->belongsTo(TempatTimbulanSampah::class, 'tts_distribusi_id', 'id');
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
