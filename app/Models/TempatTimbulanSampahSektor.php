<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempatTimbulanSampahSektor extends Model
{

    protected $fillable = [
        'tts_kategori_id',
        'nama',
    ];

    public function tempatTimbulanSampah()
    {
        return $this->hasMany(TempatTimbulanSampah::class);
    }

    public function tempatTimbulanSampahKategori()
    {
        return $this->belongsTo(TempatTimbulanSampahKategori::class, 'tts_kategori_id');
    }
}
