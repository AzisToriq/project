<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai ClassroomSeeder...');

        // 1. AMBIL GURU UNTUK WALI KELAS
        // Pastikan di tabel teachers ada kolom 'jabatan' atau ambil random aja buat dummy
        $waliKelasCandidates = DB::table('teachers')->get();

        // Kalau mau spesifik jabatan, uncomment baris bawah:
        // $waliKelasCandidates = DB::table('teachers')->where('jabatan', 'Wali Kelas')->get();

        $countGuru = $waliKelasCandidates->count();
        $this->command->info("Guru tersedia untuk Wali Kelas: " . $countGuru);

        // 2. SETTING KELAS
        $tingkats = ['1', '2', '3', '4', '5', '6']; // SD Kelas 1-6
        $paralels = ['A', 'B', 'C']; // Paralel A, B, C
        $totalKelasButuh = count($tingkats) * count($paralels); // 18 Kelas

        // 3. LOGIC SEEDING
        $waliIndex = 0;
        $classroomsData = [];

        foreach ($tingkats as $tingkat) {
            foreach ($paralels as $huruf) {

                // Ambil ID Guru secara berurutan, kalau habis ya NULL/Kosong
                $waliKelasId = null;
                if ($waliIndex < $countGuru) {
                    $waliKelasId = $waliKelasCandidates[$waliIndex]->id;
                    $waliIndex++;
                }

                // Cek apakah kelas sudah ada biar gak duplikat
                $namaKelas = $tingkat . $huruf; // Contoh: 1A

                // Pakai updateOrInsert biar aman kalau di-run berkali-kali
                DB::table('classrooms')->updateOrInsert(
                    [
                        'nama_kelas' => $namaKelas, // Kunci pencarian
                        'academic_year' => '2025/2026'
                    ],
                    [
                        'level' => $tingkat, // Sesuaikan dengan controller (level)
                        'major' => 'Umum',   // SD biasanya Umum
                        'wali_kelas_id' => $waliKelasId, // Wali kelas masuk sini oke
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );

                $this->command->info("Kelas {$namaKelas} dibuat. Wali Kelas ID: " . ($waliKelasId ?? 'KOSONG'));
            }
        }

        $this->command->info('✅ ClassroomSeeder Selesai! Total 18 Kelas diproses.');
    }
}
