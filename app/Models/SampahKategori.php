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

    public function sampahDiolahs()
    {
        return $this->hasMany(SampahDiolah::class);
    }

    public function sampahDimanfaatkans()
    {
        return $this->hasMany(SampahDimanfaatkan::class);
    }
}
