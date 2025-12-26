<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <--- PENTING: Tambahkan ini
use Carbon\Carbon;
use App\Models\TeacherAllocation;
use App\Models\Schedule; // Tambahkan ini jika ingin membersihkan jadwal sekalian

class TeacherAllocationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Memulai Proses Plotting Guru (Allocation)...');

        // 1. NONAKTIFKAN PENGECEKAN FOREIGN KEY (SOLUSI ERROR)
        Schema::disableForeignKeyConstraints();

        // 2. BERSIHKAN DATA (Termasuk data anak-anaknya biar bersih total)
        Schedule::truncate();          // Hapus Jadwal dulu (karena dia pakai allocation)
        TeacherAllocation::truncate(); // Baru hapus Allocation

        // 3. AKTIFKAN KEMBALI PENGECEKAN
        Schema::enableForeignKeyConstraints();

        // ---------------------------------------------------------
        // LOGIKA PLOTTING (SAMA SEPERTI SEBELUMNYA)
        // ---------------------------------------------------------

        // Load Data Master
        $classrooms = DB::table('classrooms')
                        ->orderBy('level', 'asc')
                        ->orderBy('nama_kelas', 'asc')
                        ->get();

        $subjects = DB::table('subjects')->get()->keyBy('code');

        $guruOlahraga = DB::table('teachers')->where('jabatan', 'Guru Olahraga')->get()->values();
        $guruAgama    = DB::table('teachers')->where('jabatan', 'Guru Mengaji')->get()->values();
        $allTeachers  = DB::table('teachers')->get();

        if ($subjects->isEmpty() || $classrooms->isEmpty() || $allTeachers->isEmpty()) {
            $this->command->error('❌ Data Master kosong. Jalankan seeder master dulu!');
            return;
        }

        $allocations = [];
        $countOlahraga = 0;
        $countAgama = 0;

        foreach ($classrooms as $classroom) {

            // Tentukan Guru Kelas (Prioritas: Wali Kelas -> Random Guru)
            $guruKelasId = $classroom->wali_kelas_id;
            if (!$guruKelasId) {
                // Jika tidak ada wali kelas, ambil random guru agar tidak error
                $guruKelasId = $allTeachers->random()->id;
            }

            // A. MAPEL UMUM
            $mapelUmumCodes = ['PKN', 'BIN', 'MTK', 'IPA', 'IPS', 'SBDP', 'BIG'];

            foreach ($mapelUmumCodes as $code) {
                if (isset($subjects[$code])) {
                    $allocations[] = [
                        'teacher_id'    => $guruKelasId,
                        'classroom_id'  => $classroom->id,
                        'subject_id'    => $subjects[$code]->id,
                        'semester'      => 'Ganjil',
                        'academic_year' => '2025/2026',
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ];
                }
            }

            // B. GURU OLAHRAGA
            if (isset($subjects['PJOK'])) {
                $guruId = $guruOlahraga->isNotEmpty()
                            ? $guruOlahraga[$countOlahraga % $guruOlahraga->count()]->id
                            : $guruKelasId;

                $allocations[] = [
                    'teacher_id'    => $guruId,
                    'classroom_id'  => $classroom->id,
                    'subject_id'    => $subjects['PJOK']->id,
                    'semester'      => 'Ganjil',
                    'academic_year' => '2025/2026',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ];
                $countOlahraga++;
            }

            // C. GURU AGAMA (PAI & MENGAJI)
            $guruAgamaId = $guruAgama->isNotEmpty()
                            ? $guruAgama[$countAgama % $guruAgama->count()]->id
                            : $guruKelasId;

            // PAI
            if (isset($subjects['PAI'])) {
                $allocations[] = [
                    'teacher_id'    => $guruAgamaId,
                    'classroom_id'  => $classroom->id,
                    'subject_id'    => $subjects['PAI']->id,
                    'semester'      => 'Ganjil',
                    'academic_year' => '2025/2026',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ];
            }

            // Mengaji (NGJ)
            if (isset($subjects['NGJ'])) {
                $allocations[] = [
                    'teacher_id'    => $guruAgamaId,
                    'classroom_id'  => $classroom->id,
                    'subject_id'    => $subjects['NGJ']->id,
                    'semester'      => 'Ganjil',
                    'academic_year' => '2025/2026',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ];
            }
            $countAgama++;
        }

        // INSERT MASSAL
        if (!empty($allocations)) {
            $chunks = array_chunk($allocations, 500);
            foreach ($chunks as $chunk) {
                DB::table('teacher_allocations')->insert($chunk);
            }
            $this->command->info('✅ Berhasil plotting ' . count($allocations) . ' mapel ke guru.');
        } else {
            $this->command->warn('⚠️ Tidak ada data plotting. Cek kode mapel di SubjectSeeder.');
        }
    }
}
