<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // WAJIB ADA
use Carbon\Carbon;
use App\Models\TeacherAllocation;
use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Attendance; // Tambahkan Model Attendance

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Memulai ScheduleSeeder dengan Distribusi Jam Realistis...');

        // ==========================================
        // 1. BERSIHKAN JADWAL LAMA (ANTI ERROR FK)
        // ==========================================
        Schema::disableForeignKeyConstraints(); // Matikan kunci asing sementara

        Attendance::truncate(); // Hapus data absensi dulu (biar bersih)
        Schedule::truncate();   // Baru hapus jadwal

        Schema::enableForeignKeyConstraints();  // Nyalakan lagi
        // ==========================================

        // 2. DEFINISI SLOT WAKTU (40 Menit/Jam)
        $timeSlots = [
            ['07:30:00', '08:10:00'], // Jam 1 (Index 0)
            ['08:10:00', '08:50:00'], // Jam 2 (Index 1)
            ['08:50:00', '09:30:00'], // Jam 3 (Index 2)
            ['09:30:00', '10:00:00'], // ISTIRAHAT (Index 3 - Skip)
            ['10:00:00', '10:40:00'], // Jam 4 (Index 4)
            ['10:40:00', '11:20:00'], // Jam 5 (Index 5)
            ['11:20:00', '12:00:00'], // Jam 6 (Index 6)
            ['12:00:00', '12:40:00'], // Jam 7 (Index 7)
            ['12:40:00', '13:20:00'], // Jam 8 (Index 8)
        ];

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // 3. DEFINISI BEBAN JAM PER MAPEL
        $subjectWeights = [
            'Pendidikan Agama Islam' => 3,
            'Pendidikan Pancasila' => 2,
            'Bahasa Indonesia' => 6,
            'Matematika' => 5,
            'Ilmu Pengetahuan Alam' => 3,
            'Ilmu Pengetahuan Sosial' => 3,
            'Seni Budaya dan Prakarya' => 3,
            'Pendidikan Jasmani' => 3, // Disesuaikan agar match stringnya
            'Bahasa Inggris' => 2,
            'Mengaji' => 2,
            'default' => 2
        ];

        // 4. PROSES PER KELAS
        $classrooms = Classroom::all();

        foreach ($classrooms as $classroom) {
            $this->command->info("🔹 Memproses jadwal kelas: {$classroom->nama_kelas}");

            // Ambil alokasi guru
            $allocations = TeacherAllocation::with('subject')
                            ->where('classroom_id', $classroom->id)
                            ->get();

            if ($allocations->isEmpty()) {
                continue;
            }

            // Siapkan antrian mapel
            $scheduleQueue = [];

            foreach ($allocations as $alloc) {
                $mapelName = $alloc->subject->nama_mapel;
                $hoursNeeded = 2; // Default

                foreach ($subjectWeights as $key => $weight) {
                    if (stripos($mapelName, $key) !== false) {
                        $hoursNeeded = $weight;
                        break;
                    }
                }

                // Masukkan ke antrian
                for ($i = 0; $i < $hoursNeeded; $i++) {
                    $scheduleQueue[] = $alloc->id;
                }
            }

            // Acak urutan mapel
            shuffle($scheduleQueue);

            // 5. ISI SLOT WAKTU
            $queueIndex = 0;
            $totalQueue = count($scheduleQueue);

            foreach ($days as $day) {
                foreach ($timeSlots as $slotIndex => $time) {

                    // Skip Istirahat
                    if ($slotIndex == 3) continue;

                    // Skip Jumat siang
                    if ($day == 'Jumat' && $slotIndex > 5) continue;

                    // Stop jika antrian habis
                    if ($queueIndex >= $totalQueue) break 2;

                    $allocationId = $scheduleQueue[$queueIndex];

                    Schedule::create([
                        'teacher_allocation_id' => $allocationId,
                        'day' => $day,
                        'start_time' => $time[0],
                        'end_time' => $time[1],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    $queueIndex++;
                }
            }
        }

        $this->command->info('✅ ScheduleSeeder Selesai! Jadwal aman terkendali.');
    }
}
