<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class JadwalSiswaController extends Controller
{
    public function index()
    {
        // 1. Ambil User yang login
        $user = Auth::user();

        // 2. Ambil Data Siswa dari relasi User
        // Pastikan di User.php sudah ada method public function student()
        $student = $user->student;

        // --- SAFETY CHECK ---
        // Jika akun ini tidak terhubung ke data siswa ATAU siswa belum masuk kelas
        if (!$student || !$student->classroom_id) {
            // Kirim collection kosong agar View tidak error
            return view('siswa.jadwal.index', ['schedules' => collect([])]);
        }

        // 3. QUERY UTAMA JADWAL
        $schedules = Schedule::query()
            // Filter: Cari jadwal yang teacher_allocation-nya milik kelas si siswa
            ->whereHas('teacher_allocation', function ($q) use ($student) {
                $q->where('classroom_id', $student->classroom_id);
            })
            // Eager Loading: Ambil data relasi biar tidak berat (N+1 Problem)
            ->with([
                'teacher_allocation.subject',       // Ambil Mapel
                'teacher_allocation.teacher.user'   // Ambil Guru -> User (buat nama akun)
            ])
            // Sorting 1: Urutkan Hari (Senin s/d Sabtu)
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            // Sorting 2: Urutkan Jam (Pagi -> Siang)
            ->orderBy('start_time', 'asc')
            ->get();

        // 4. Return ke View
        return view('siswa.jadwal.index', compact('schedules'));
    }
}
