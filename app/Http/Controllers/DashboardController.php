<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Import Model (Namespace disederhanakan)
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\TeacherAllocation;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\SppPayment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $data = [];

        // Mapping Hari Inggris ke Indonesia
        $hariMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        $namaHari = $hariMap[date('l')] ?? date('l');

        // ==========================================
        // LOGIC KHUSUS ADMIN
        // ==========================================
        if ($role === 'admin') {
            $data['total_siswa'] = Student::count();
            $data['total_guru'] = Teacher::count();
            $data['total_kelas'] = Classroom::count();
            $data['total_mapel'] = Subject::count();
        }

        // ==========================================
        // LOGIC KHUSUS GURU
        // ==========================================
        elseif ($role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();

            if ($teacher) {
                $data['jumlah_kelas'] = TeacherAllocation::where('teacher_id', $teacher->id)->count();

                // Menggunakan kolom 'day' sesuai database
                $data['jadwal_hari_ini'] = Schedule::where('day', $namaHari)
                    ->whereHas('teacher_allocation', function($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->with(['teacher_allocation.classroom', 'teacher_allocation.subject'])
                    ->orderBy('start_time', 'asc')
                    ->get();
            } else {
                $data['jumlah_kelas'] = 0;
                $data['jadwal_hari_ini'] = [];
            }
        }

        // ==========================================
        // LOGIC KHUSUS SISWA
        // ==========================================
        elseif ($role === 'student') {
            $student = Student::where('user_id', $user->id)->first();

            if ($student && $student->classroom_id) {
                // Menggunakan kolom 'day' sesuai database
                $data['jadwal_hari_ini'] = Schedule::where('day', $namaHari)
                    ->whereHas('teacher_allocation', function($q) use ($student) {
                        $q->where('classroom_id', $student->classroom_id);
                    })
                    ->with(['teacher_allocation.teacher', 'teacher_allocation.subject'])
                    ->orderBy('start_time', 'asc')
                    ->get();
            } else {
                $data['jadwal_hari_ini'] = [];
            }
        }

        // ==========================================
        // LOGIC KHUSUS ORANG TUA
        // ==========================================
        elseif ($role === 'parent') {
            // Ambil anak pertama yang terhubung dengan akun ini
            $anak = Student::where('parent_user_id', $user->id)
                        ->with('classroom')
                        ->first();

            $data['anak'] = $anak;

            if ($anak) {
                // UPDATE PENTING: Hanya ambil tagihan yang SUDAH DITERBITKAN (is_published = true)
                $data['tagihan_spp'] = SppPayment::where('student_id', $anak->id)
                    ->where('is_published', true) // <--- Filter Draft vs Published
                    ->orderBy('year', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();
            } else {
                $data['tagihan_spp'] = [];
            }

            if ($anak) {
                $today = Carbon::now()->format('Y-m-d');
                $attendance = Attendance::where('student_id', $anak->id)
                                ->where('date', $today)
                                ->first();

                $data['absensi_hari_ini'] = $attendance ? $attendance->status : 'Belum Absen';
            } else {
                $data['absensi_hari_ini'] = '-';
            }
        }

        return view('dashboard', $data);
    }

    /**
     * FITUR UPLOAD BUKTI BAYAR (KHUSUS ORTU)
     * Ini melengkapi siklus pembayaran SPP.
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'proof_file' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048', // Maks 2MB
        ]);

        $payment = SppPayment::findOrFail($id);

        // Pastikan yang upload adalah orang tua dari siswa pemilik tagihan
        $user = Auth::user();
        $student = Student::where('parent_user_id', $user->id)->where('id', $payment->student_id)->first();

        if (!$student) {
            return back()->with('error', 'Anda tidak memiliki akses ke tagihan ini.');
        }

        // Proses Upload File
        if ($request->hasFile('proof_file')) {
            // Simpan ke folder: storage/app/public/spp-proofs
            $path = $request->file('proof_file')->store('spp-proofs', 'public');

            // Update Database
            $payment->update([
                'proof_file' => $path,
                'status' => 'pending', // Ubah status jadi Pending (Menunggu Verifikasi Admin)
                'note' => null // Reset catatan penolakan jika ada
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diupload! Tunggu verifikasi Admin.');
    }
}
