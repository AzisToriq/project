<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan updateOrCreate agar tidak error jika data sudah ada
        User::updateOrCreate(
            ['username' => 'admin'], // 1. Cari user dengan username 'admin'
            [                        // 2. Jika ada: Update data ini. Jika tidak ada: Buat baru.
                'name' => 'Admin Sekolah',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'), // Password login: password
                'role' => 'admin',
            ]
        );
    }
}
