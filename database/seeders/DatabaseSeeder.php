<?php

namespace Database\Seeders;

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
        $roles = ['Administrator', 'Operator Sumber Sampah', 'Operator Kumpulan Sampah', 'DLH Kota/Kabupaten', 'Supir'];
        UserRole::insert(array_map(function($role) {
            return [
                'role_name' => $role,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, $roles));
        
        // Define default users
        User::insert([
            [
                'id' => Str::uuid()->toString(), // Generate UUID
                'nik' => '0',
                'fullname' => 'Admin SampahKita Jabar',
                'user_role_id' => 1, // Default role for super admin
                'phone_number' => '080000000000',
                'email' => 'admin@sampahkitajabar.id',
                'password' => Hash::make('P4$$word_admin_sampahkita_jabar'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
