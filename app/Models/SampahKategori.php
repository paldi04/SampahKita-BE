<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampahKategori extends Model
{
    protected $fillable = [
        'nama',
    ];

    public function sampahMasuks()
    {
        return $this->hasMany(SampahMasuk::class);
    }
}
