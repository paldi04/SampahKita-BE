<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempatTimbulanSampahKategori extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'nama',
    ];

    public function tempatTimbulanSampah()
    {
        return $this->hasMany(TempatTimbulanSampah::class);
    }

    public function tempatTimbulanSampahSektor()
    {
        return $this->hasMany(TempatTimbulanSampahSektor::class);
    }
}
