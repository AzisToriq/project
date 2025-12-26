<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYears = [
            [
                'name' => '2024/2025',
                'semester' => 'Genap',
                'start_date' => '2025-01-06',
                'end_date' => '2025-06-20',
                'is_active' => false,
            ],
            [
                'name' => '2025/2026', // SINKRONISASI: Samain dengan seeder Kelas & Plotting
                'semester' => 'Ganjil',
                'start_date' => '2025-07-14',
                'end_date' => '2025-12-19',
                'is_active' => true, // INI YANG AKTIF
            ],
            [
                'name' => '2025/2026',
                'semester' => 'Genap',
                'start_date' => '2026-01-05',
                'end_date' => '2026-06-19',
                'is_active' => false,
            ],
        ];

        foreach ($schoolYears as $year) {
            DB::table('school_years')->updateOrInsert(
                [
                    'name' => $year['name'],
                    'semester' => $year['semester']
                ],
                array_merge($year, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }

        $this->command->info('✅ Seeder school_years berhasil ditambahkan!');
        $this->command->info('Total: ' . count($schoolYears) . ' tahun ajaran');
        $this->command->info('Tahun ajaran aktif: 2025/2026 Ganjil');
    }
}
