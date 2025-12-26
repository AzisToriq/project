<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;

class PerkembanganController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Anak berdasarkan User Login (Ortu)
        $parent = Auth::user();
        $anak = Student::where('parent_user_id', $parent->id)->with('classroom')->first();

        // Jika data anak belum di-link
        if (!$anak) {
            return redirect()->route('ortu.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // 2. Hitung Statistik Absensi (Status: H, S, I, A)
        // Kita hitung total dari tabel attendance berdasarkan student_id
        $hadir = Attendance::where('student_id', $anak->id)->where('status', 'H')->count();
        $sakit = Attendance::where('student_id', $anak->id)->where('status', 'S')->count();
        $izin  = Attendance::where('student_id', $anak->id)->where('status', 'I')->count();
        $alpha = Attendance::where('student_id', $anak->id)->where('status', 'A')->count();

        $total_pertemuan = $hadir + $sakit + $izin + $alpha;

        // Hindari pembagian dengan nol
        $persentase_kehadiran = $total_pertemuan > 0
                                ? round(($hadir / $total_pertemuan) * 100)
                                : 0;

        // 3. Ambil History Absensi Terakhir (5 Data Terbaru)
        // Kita load relasi ke teacher_allocation -> subject supaya tahu mapel apa
        $riwayat_absensi = Attendance::with(['teacher_allocation.subject', 'teacher_allocation.teacher'])
                            ->where('student_id', $anak->id)
                            ->orderBy('date', 'desc')
                            ->take(5)
                            ->get();

        // 4. Ambil Nilai Terakhir (Opsional, buat ditampilkan juga)
        $nilai_terbaru = Grade::with(['teacher_allocation.subject'])
                            ->where('student_id', $anak->id)
                            ->latest()
                            ->take(5)
                            ->get();

        return view('ortu.perkembangan.index', compact(
            'anak',
            'hadir', 'sakit', 'izin', 'alpha',
            'persentase_kehadiran',
            'riwayat_absensi',
            'nilai_terbaru'
        ));
    }
}
