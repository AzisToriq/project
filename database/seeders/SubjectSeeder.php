<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['code' => 'PAI', 'nama_mapel' => 'Pendidikan Agama Islam'],
            ['code' => 'PKN', 'nama_mapel' => 'Pendidikan Pancasila'],
            ['code' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia'],
            ['code' => 'MTK', 'nama_mapel' => 'Matematika'],
            ['code' => 'IPA', 'nama_mapel' => 'Ilmu Pengetahuan Alam'],
            ['code' => 'IPS', 'nama_mapel' => 'Ilmu Pengetahuan Sosial'],
            ['code' => 'SBDP', 'nama_mapel' => 'Seni Budaya dan Prakarya'],
            ['code' => 'PJOK', 'nama_mapel' => 'PJOK'], // Olahraga
            ['code' => 'BIG', 'nama_mapel' => 'Bahasa Inggris'],
            ['code' => 'NGJ', 'nama_mapel' => 'Mengaji'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['code' => $subject['code']], // Check by code
                array_merge($subject, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ])
            );
        }

        $this->command->info('✅ SubjectSeeder Selesai!');
    }
}
