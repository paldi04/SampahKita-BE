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
                'id' => 1,
                'code' => 'admin',
                'name' => 'Administrator',
            ], [
                'id' => 2,
                'code' => 'oss',
                'name' => 'Operator Sumber Sampah',
            ], [
                'id' => 3,
                'code' => 'oks',
                'name' => 'Operator Kumpulan Sampah',
            ], [
                'id' => 4,
                'code' => 'dlh',
                'name' => 'DLH Kota/Kabupaten',
            ], [
                'id' => 5,
                'code' => 'supir',
                'name' => 'Supir',
            ]
        ];
        UserRole::insert(array_map(function($role) {
            return [
                'code' => $role['code'],
                'name' => $role['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $userRoles));
        
        // Define default users
        User::insert([
            [
                'id' => Str::uuid()->toString(), // Generate UUID
                'nik' => '0',
                'name' => 'Admin SampahKita Jabar',
                'user_role_id' => 1, // Default role for super admin
                'phone_number' => '080000000000',
                'email' => 'admin@sampahkitajabar.id',
                'password' => Hash::make('P4$$word_admin_sampahkita_jabar'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // Define default tempat timbulan sampah kategori
        $timbulanSampahKategori = [
            [
                'id' => 1,
                'code' => 'tss',
                'name' => 'Tempat Sumber sampah',
            ], 
            [
                'id' => 2,
                'code' => 'tks',
                'name' => 'Tempat Kumpulan Sampah',
            ], 
            [
                'id' => 3,
                'code' => 'tpa',
                'name' => 'Tempat Pembuangan Akhir',
            ]
        ];
        TempatTimbulanSampahKategori::insert(array_map(function($kategori) {
            return [
                'code' => $kategori['code'],
                'name' => $kategori['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $timbulanSampahKategori));

        // Define default tempat timbulan sampah sektor
        $TempatTimbulanSampahSektor = [
            [
                'tts_kategori_id' => 1,
                'name' => 'Sekolah',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Perguruan Tinggi',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Pondok Pesantren',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Perkantoran',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Pasar',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Fasilitas Pelayanan kesehatan (RS/Puskesmas/Klinik)',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Retail Moden/Swalayan/Mini Market',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Pertokoan/Kios/Warung',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Industri',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Penginapan/Hotel/Wisma',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Rumah Makan/Restoran',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Lembaga Permasyarakatan (Lapas)',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Terminal Bus/Angkot',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Stasiun Kereta Api',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Pelabuhan Penumpang',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Bandara Udara',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Tempat Ibadah',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Tempat Wisata',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Taman Kota',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Hutan Kota',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Pemukiman',
            ],
            [
                'tts_kategori_id' => 1,
                'name' => 'Kegiatan Bersih Sampah',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'TPS',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'TPST',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'TPS3R',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Bank Sampah Induk',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Bank Sampah Unit',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Komposting',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Rumah Kompos',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Pusat Olah Organik',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Pusat Daur Ulang',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'TPST diluar TPA',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'ITF',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Biodigester',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Incinerator',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Pirolisis',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'Gasifikasi',
            ],
            [
                'tts_kategori_id' => 2,
                'name' => 'RDF',
            ],
            [
                'tts_kategori_id' => 2,
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
