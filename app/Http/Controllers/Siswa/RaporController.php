<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Import Model
use App\Models\Student;
use App\Models\Grade;
use App\Models\TeacherAllocation;

class RaporController extends Controller
{
    // ==========================================
    // 1. HALAMAN DEPAN (COVER / DASHBOARD RAPOR)
    // ==========================================
    public function index()
    {
        $user = Auth::user();

        // Cari Data Siswa
        $student = Student::with(['classroom.waliKelas', 'user'])
                        ->where('user_id', $user->id)
                        ->first();

        // --- SAFETY CHECK 1: User punya data siswa? ---
        if (!$student) {
            return redirect()->back()->with('error', 'Data profil siswa tidak ditemukan.');
        }

        // --- SAFETY CHECK 2: Siswa masuk kelas? ---
        if (!$student->classroom) {
            return view('siswa.rapor.blocked', [
                'title'   => 'Belum Masuk Kelas',
                'message' => 'Anda belum terdaftar di kelas manapun. Silakan hubungi Tata Usaha.'
            ]);
        }

        // --- LOGIC UTAMA: CEK STATUS PUBLIKASI RAPOR ---
        if ($student->classroom->is_rapor_published == 0) {
            return view('siswa.rapor.blocked', [
                'title'   => 'Rapor Belum Dibuka',
                'message' => 'Wali Kelas belum mempublikasikan rapor. Harap tunggu info pembagian rapor dari sekolah.'
            ]);
        }

        // Jika lolos, TAMPILKAN HALAMAN COVER (index.blade.php yang baru kamu buat)
        return view('siswa.rapor.index', compact('student'));
    }

    // ==========================================
    // 2. HALAMAN DETAIL (CETAK KERTAS A4)
    // ==========================================
    public function print()
    {
        $user = Auth::user();

        $student = Student::with(['classroom.waliKelas', 'user'])
                        ->where('user_id', $user->id)
                        ->first();

        // --- SECURITY CHECK ULANG (Agar tidak ditembak via URL) ---
        if (!$student || !$student->classroom || $student->classroom->is_rapor_published == 0) {
            return redirect()->route('siswa.rapor'); // Tendang balik ke index
        }

        // --- AMBIL DATA RAPOR LENGKAP ---

        // 1. Ambil Mapel
        $allocations = TeacherAllocation::with(['subject', 'teacher.user'])
                        ->where('classroom_id', $student->classroom_id)
                        ->get();

        // 2. Ambil Nilai
        $rawGrades = Grade::where('student_id', $student->id)->get();

        // 3. Format Nilai
        $grades = [];
        foreach($rawGrades as $g) {
            $type = strtolower($g->type);
            $grades[$g->teacher_allocation_id][$type] = $g->score;
        }

        // 4. Data Absensi (Dummy/Manual agar tidak error)
        $absensi = [
            'sakit' => 0,
            'izin'  => 0,
            'alpha' => 0
        ];

        // 5. Data Ekskul (Dummy/Manual agar tidak error)
        $ekskul = [
            [
                'nama' => 'Pramuka (Wajib)',
                'predikat' => 'B',
                'keterangan' => 'Aktif mengikuti kegiatan.'
            ]
        ];

        // TAMPILKAN VIEW A4 (Shared View dari Admin)
        return view('admin.datamaster.students.rapor', compact('student', 'allocations', 'grades', 'absensi', 'ekskul'));
    }
}
