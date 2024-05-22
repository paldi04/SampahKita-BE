<?php

namespace Database\Seeders;

use App\Models\SampahKategori;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SampahSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Define default tempat timbulan sampah kategori
        $sampahKategori = [
            [
                'nama' => 'Anorganik Guna Ulang',
            ], 
            [
                'nama' => 'Anorganik Daur Ulang',
            ], 
            [
                'nama' => 'Organik',
            ],
            [
                'nama' => 'B3',
            ],
            [
                'nama' => 'Residu',
            ],
            [
                'nama' => 'Tercampur',
            ]
        ];
        SampahKategori::insert(array_map(function($kategori) {
            return [
                'nama' => $kategori['nama'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $sampahKategori));

    }
}
