<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. DATA MASTER SYSTEM (Admin & Pengaturan)
            UserSeeder::class,       // Bikin Admin (PENTING BUAT LOGIN)
            SettingSeeder::class,    // Bikin Nama Sekolah, dll

            // 2. DATA MASTER AKADEMIK
            SchoolYearSeeder::class, // Tahun Ajaran
            SubjectSeeder::class,    // Mata Pelajaran

            // 3. PIHAK SEKOLAH
            TeacherSeeder::class,    // Guru (User + Profil)
            ClassroomSeeder::class,  // Kelas (Butuh Guru)
            StudentSeeder::class,    // Siswa + Ortu (Butuh Kelas)

            // 4. PLOTTING & JADWAL
            TeacherAllocationSeeder::class, // Plotting (Butuh Guru, Mapel, Kelas)
            ScheduleSeeder::class,          // Jadwal (Butuh Plotting)
        ]);
    }
}
