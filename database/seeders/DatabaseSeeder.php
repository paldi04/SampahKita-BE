<?php

namespace Database\Seeders;

use App\Models\TempatTimbulanSampahKategori;
use App\Models\TempatTimbulanSampahSektor;
use Illuminate\Database\Seeder;
use App\Models\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Define default user roles
        $userRoles = [
            [
                'id' => 'admin',
                'name' => 'Administrator',
            ], [
                'id' => 'oss',
                'name' => 'Operator Sumber Sampah',
            ], [
                'id' => 'oks',
                'name' => 'Operator Kumpulan Sampah',
            ], [
                'id' => 'dlh',
                'name' => 'DLH Kota/Kabupaten',
            ], [
                'id' => 'supir',
                'name' => 'Supir',
            ]
        ];
        UserRole::insert(array_map(function($role) {
            return [
                'id' => $role['id'],
                'name' => $role['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $userRoles));
        
        // Define default users
        User::insert([
            [
                'id' => Str::uuid()->toString(), // Generate UUID
                'name' => 'Admin Sampah Kita Jabar',
                'user_role_id' => 'admin', // Default role for super admin
                'phone_number' => '080000000000',
                'email' => 'admin@sampahkitajabar.id',
                'password' => Hash::make('P4$$word_admin_sampahkita_jabar'),
                'status' => 'verified',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // Define default tempat timbulan sampah kategori
        $timbulanSampahKategori = [
            [
                'id' => 'tss',
                'name' => 'Tempat Sumber sampah',
            ], 
            [
                'id' => 'tks',
                'name' => 'Tempat Kumpulan Sampah',
            ], 
            [
                'id' => 'tpa',
                'name' => 'Tempat Pembuangan Akhir',
            ]
        ];
        TempatTimbulanSampahKategori::insert(array_map(function($kategori) {
            return [
                'id' => $kategori['id'],
                'name' => $kategori['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $timbulanSampahKategori));

        // Define default tempat timbulan sampah sektor
        $TempatTimbulanSampahSektor = [
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Sekolah',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Perguruan Tinggi',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Pondok Pesantren',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Perkantoran',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Pasar',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Fasilitas Pelayanan kesehatan (RS/Puskesmas/Klinik)',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Retail Moden/Swalayan/Mini Market',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Pertokoan/Kios/Warung',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Industri',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Penginapan/Hotel/Wisma',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Rumah Makan/Restoran',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Lembaga Permasyarakatan (Lapas)',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Terminal Bus/Angkot',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Stasiun Kereta Api',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Pelabuhan Penumpang',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Bandara Udara',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Tempat Ibadah',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Tempat Wisata',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Taman Kota',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Hutan Kota',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Pemukiman',
            ],
            [
                'tts_kategori_id' => 'tss',
                'name' => 'Kegiatan Bersih Sampah',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'TPS',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'TPST',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'TPS3R',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Bank Sampah Induk',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Bank Sampah Unit',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Komposting',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Rumah Kompos',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Pusat Olah Organik',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Pusat Daur Ulang',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'TPST diluar TPA',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'ITF',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Biodigester',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Incinerator',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Pirolisis',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Gasifikasi',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'RDF',
            ],
            [
                'tts_kategori_id' => 'tks',
                'name' => 'Rumah Maggot',
            ],
        ];
        TempatTimbulanSampahSektor::insert(array_map(function($sektor) {
            return [
                'tts_kategori_id' => $sektor['tts_kategori_id'],
                'name' => $sektor['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $TempatTimbulanSampahSektor));
    }
}
