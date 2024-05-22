<?php

namespace Database\Seeders;

use App\Models\TempatTimbulanSampahKategori;
use App\Models\TempatTimbulanSampahSektor;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TempatTimbulanSampahSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Define default tempat timbulan sampah kategori
        $timbulanSampahKategori = [
            [
                'id' => 'tss',
                'nama' => 'Tempat Sumber sampah',
            ], 
            [
                'id' => 'tks',
                'nama' => 'Tempat Kumpulan Sampah',
            ], 
            [
                'id' => 'tpa',
                'nama' => 'Tempat Pembuangan Akhir',
            ]
        ];
        TempatTimbulanSampahKategori::insert(array_map(function($kategori) {
            return [
                'id' => $kategori['id'],
                'nama' => $kategori['nama'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $timbulanSampahKategori));

        // Define default tempat timbulan sampah sektor
        $TempatTimbulanSampahSektor = [
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Sekolah',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Perguruan Tinggi',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Pondok Pesantren',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Perkantoran',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Pasar',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Fasilitas Pelayanan kesehatan (RS/Puskesmas/Klinik)',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Retail Moden/Swalayan/Mini Market',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Pertokoan/Kios/Warung',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Industri',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Penginapan/Hotel/Wisma',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Rumah Makan/Restoran',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Lembaga Permasyarakatan (Lapas)',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Terminal Bus/Angkot',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Stasiun Kereta Api',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Pelabuhan Penumpang',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Bandara Udara',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Tempat Ibadah',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Tempat Wisata',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Taman Kota',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Hutan Kota',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Pemukiman',
            ],
            [
                'tts_kategori_id' => 'tss',
                'nama' => 'Kegiatan Bersih Sampah',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'TPS',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'TPST',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'TPS3R',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Bank Sampah Induk',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Bank Sampah Unit',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Komposting',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Rumah Kompos',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Pusat Olah Organik',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Pusat Daur Ulang',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'TPST diluar TPA',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'ITF',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Biodigester',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Incinerator',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Pirolisis',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Gasifikasi',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'RDF',
            ],
            [
                'tts_kategori_id' => 'tks',
                'nama' => 'Rumah Maggot',
            ],
        ];
        TempatTimbulanSampahSektor::insert(array_map(function($sektor) {
            return [
                'tts_kategori_id' => $sektor['tts_kategori_id'],
                'nama' => $sektor['nama'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $TempatTimbulanSampahSektor));
    }
}
