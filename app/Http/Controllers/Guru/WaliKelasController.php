<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Model Wajib
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\TeacherAllocation; // Buat ambil Mapel di Preview
use App\Models\Grade;             // Buat ambil Nilai di Preview

class WaliKelasController extends Controller
{
    /**
     * 1. DASHBOARD MONITORING
     * Menampilkan daftar siswa & tombol rilis rapor
     */
    public function index()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Cari Kelas dimana guru ini menjabat sebagai Wali Kelas
        $classroom = Classroom::where('wali_kelas_id', $teacher->id)->first();

        // Jika bukan wali kelas, lempar ke tampilan 'bukan wali kelas'
        if (!$classroom) {
            return view('guru.wali-kelas.non-wali');
        }

        // Ambil daftar siswa
        $students = Student::where('classroom_id', $classroom->id)
                        ->orderBy('nama_lengkap')
                        ->get();

        return view('guru.wali-kelas.index', compact('classroom', 'students'));
    }

    /**
     * 2. TOGGLE: Buka/Tutup Akses Rapor
     */
    public function toggleRapor($classroom_id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Security Check: Pastikan dia beneran Wali Kelas dari kelas ini
        $classroom = Classroom::where('id', $classroom_id)
                        ->where('wali_kelas_id', $teacher->id)
                        ->first();

        if (!$classroom) {
            return back()->with('error', 'Anda bukan Wali Kelas dari kelas ini.');
        }

        // Toggle Status
        $classroom->is_rapor_published = !$classroom->is_rapor_published;
        $classroom->save();

        $status = $classroom->is_rapor_published ? 'DIBUKA' : 'DITUTUP';
        return back()->with('success', "Rapor kelas {$classroom->nama_kelas} berhasil {$status}.");
    }

    /**
     * 3. PREVIEW RAPOR (TOMBOL MATA)
     * Ini logic untuk melihat rapor utuh siswa
     */
    public function show($student_id)
    {
        // A. Validasi Wali Kelas
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $student = Student::with(['classroom.waliKelas', 'user'])->findOrFail($student_id);

        if ($student->classroom->wali_kelas_id != $teacher->id) {
            abort(403, 'Anda tidak memiliki akses melihat rapor siswa ini.');
        }

        // B. Ambil Mapel (Termasuk PJOK/Mengaji dari tabel subjects)
        $allocations = TeacherAllocation::with(['subject', 'teacher.user'])
                        ->where('classroom_id', $student->classroom_id)
                        ->get();

        // C. Ambil Nilai
        $rawGrades = Grade::where('student_id', $student->id)->get();
        $grades = [];
        foreach($rawGrades as $g) {
            $grades[$g->teacher_allocation_id][strtolower($g->type)] = $g->score;
        }

        // D. Data Absensi (Manual 0 agar tidak error karena tabel belum ada)
        $absensi = ['sakit' => 0, 'izin' => 0, 'alpha' => 0];

        // E. Data Ekskul (Manual Array agar tidak error)
        $ekskul = [
            ['nama' => 'Pramuka', 'predikat' => 'B', 'keterangan' => 'Aktif']
        ];

        // F. Panggil View Rapor yang ada di folder ADMIN
        return view('admin.student.rapor', compact('student', 'allocations', 'grades', 'absensi', 'ekskul'));
    }
}
