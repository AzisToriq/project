<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ConnectParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚑 MEMULAI OPERASI PERBAIKAN DATA ORTU & SISWA...');

        // 1. Ambil semua data siswa
        $students = Student::all();

        if ($students->isEmpty()) {
            $this->command->warn('Data Siswa kosong. Tidak ada yang perlu diperbaiki.');
            return;
        }

        $countFixed = 0;

        foreach ($students as $student) {
            $nis = $student->nis;

            // Cari User Ortu berdasarkan pola username lama (ortu_NIS)
            $parentUser = User::where('username', 'ortu_' . $nis)->first();

            if ($parentUser) {
                // OPERASI 1: Update Email Ortu jika masih NULL (Biar bisa login)
                if (is_null($parentUser->email)) {
                    $emailBaru = "ortu.{$nis}@sekolah.com";

                    // Cek biar ga error duplicate entry
                    if (!User::where('email', $emailBaru)->exists()) {
                        $parentUser->email = $emailBaru;
                        $parentUser->save();
                        $this->command->info("✅ Email Ortu {$parentUser->username} diupdate: {$emailBaru}");
                    }
                }

                // OPERASI 2: Sambungkan (Link) ID Ortu ke Tabel Student
                // Ini yang bikin relasi Siswa -> Ortu nyambung
                if (is_null($student->parent_user_id)) {
                    $student->parent_user_id = $parentUser->id;
                    $student->save();
                    $countFixed++;
                }
            }
        }

        $this->command->info("🎉 SELESAI! Total {$countFixed} hubungan Siswa-Ortu berhasil diperbaiki.");
    }
}
