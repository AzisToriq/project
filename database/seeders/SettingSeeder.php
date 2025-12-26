<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['key' => 'school_name', 'value' => 'SMA Nusantara'],
            ['key' => 'school_address', 'value' => 'Jl. Pendidikan No. 1, Jakarta'],
            ['key' => 'school_phone', 'value' => '021-12345678'],
            ['key' => 'school_email', 'value' => 'admin@sekolah.sch.id'],
            ['key' => 'academic_year', 'value' => '2025/2026'],
            ['key' => 'semester', 'value' => 'Ganjil'],
        ];

        foreach ($data as $item) {
            Setting::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
