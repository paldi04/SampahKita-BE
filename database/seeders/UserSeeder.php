<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
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
                'nama' => 'Administrator',
            ], [
                'id' => 'oss',
                'nama' => 'Operator Sumber Sampah',
            ], [
                'id' => 'oks',
                'nama' => 'Operator Kumpulan Sampah',
            ], [
                'id' => 'dlh',
                'nama' => 'DLH Kota/Kabupaten',
            ], [
                'id' => 'supir',
                'nama' => 'Supir',
            ]
        ];
        UserRole::insert(array_map(function($role) {
            return [
                'id' => $role['id'],
                'nama' => $role['nama'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $userRoles));
        
        // Define default users
        User::insert([
            [
                'id' => Str::uuid()->toString(), // Generate UUID
                'nama' => 'Admin Sampah Kita Jabar',
                'user_role_id' => 'admin', // Default role for super admin
                'nomor_telepon' => '080000000000',
                'email' => 'admin@sampahkitajabar.id',
                'password' => Hash::make('P4$$word_admin_sampahkita_jabar'),
                'status' => 'terverifikasi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
