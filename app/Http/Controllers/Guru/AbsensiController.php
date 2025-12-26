<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\TeacherAllocation;
use App\Models\Teacher;

class AbsensiController extends Controller
{
    // =========================================================================
    // 1. DASHBOARD FOLDER KELAS (Step 1: Pilih Kelas)
    // =========================================================================
    public function index()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Ambil Daftar Kelas Unik yang diajar guru ini
        // Kita ambil dari allocation, lalu pluck classroom-nya biar jadi list kelas
        $classrooms = TeacherAllocation::with('classroom')
            ->where('teacher_id', $teacher->id)
            ->get()
            ->pluck('classroom')
            ->unique('id'); // Cegah duplikat folder kelas

        return view('guru.absensi.index', compact('classrooms'));
    }

    // =========================================================================
    // 2. LIST MAPEL (Step 2: Pilih Mapel di dalam Kelas)
    // =========================================================================
    public function showMapel($classroom_id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Ambil Mapel apa saja yang diajar guru ini di kelas tersebut
        $allocations = TeacherAllocation::with(['subject', 'classroom'])
            ->where('teacher_id', $teacher->id)
            ->where('classroom_id', $classroom_id)
            ->get();

        if ($allocations->isEmpty()) {
            return back()->with('error', 'Tidak ada mata pelajaran di kelas ini.');
        }

        // Ambil data kelas dari allocation pertama buat judul
        $classroom = $allocations->first()->classroom;

        return view('guru.absensi.list_mapel', compact('allocations', 'classroom'));
    }

    // =========================================================================
    // 3. FORM INPUT (Step 3: Isi Absen dengan Logic Pintar)
    // =========================================================================
    // Function ini menangani input dari Jadwal maupun Manual
    public function create(Request $request, $allocation_id)
    {
        $allocation = TeacherAllocation::with(['classroom', 'subject'])->findOrFail($allocation_id);

        // 1. Tentukan Tanggal (Default Hari Ini jika tidak ada parameter)
        $tanggal = $request->query('date', Carbon::now()->format('Y-m-d'));
        $hariDipilih = Carbon::parse($tanggal)->locale('id')->translatedFormat('l');

        // 2. Cek Jadwal: Apakah hari ini ada jadwal buat mapel ini?
        $jadwalHariIni = Schedule::where('teacher_allocation_id', $allocation_id)
                        ->where('day', $hariDipilih)
                        ->first();

        // 3. Status Jadwal (Untuk Alert di View)
        // Jika jadwal ketemu -> SESUAI. Jika tidak -> MANUAL.
        $statusJadwal = $jadwalHariIni ? 'SESUAI' : 'MANUAL';

        // 4. Ambil Data Siswa
        $students = Student::where('classroom_id', $allocation->classroom_id)
            ->orderBy('nama_lengkap')
            ->get();

        // 5. Ambil Data Absen Existing (Jika mau edit/lanjut absen)
        // Kita cek berdasarkan allocation & tanggal
        $existingAbsensi = Attendance::where('teacher_allocation_id', $allocation_id)
            ->whereDate('date', $tanggal)
            ->get()
            ->keyBy('student_id');

        return view('guru.absensi.input', compact(
            'allocation',
            'students',
            'tanggal',
            'existingAbsensi',
            'statusJadwal'
        ));
    }

    // =========================================================================
    // 4. PROSES SIMPAN (NO KORUP ENGINE)
    // =========================================================================
    public function store(Request $request)
    {
        $request->validate([
            'teacher_allocation_id' => 'required|exists:teacher_allocations,id',
            'date'                  => 'required|date',
            'absensi'               => 'required|array',
        ]);

        $tanggal = $request->date;
        $allocationId = $request->teacher_allocation_id;

        DB::transaction(function () use ($request, $allocationId, $tanggal) {

            // LOGIC SCHEDULE ID:
            // Kita cari Schedule ID berdasarkan HARI tanggal input.
            // Ini PENTING biar data attendance tetap punya induk jadwal.
            $hari = Carbon::parse($tanggal)->locale('id')->translatedFormat('l');

            $schedule = Schedule::where('teacher_allocation_id', $allocationId)
                        ->where('day', $hari)
                        ->first();

            // Jika jadwal hari itu gak ada (Absen Manual/Susulan di hari libur),
            // Kita ambil sembarang jadwal dari mapel ini sebagai induk (Best Effort),
            // atau biarkan null (tergantung kebijakan, disini kita ambil first biar aman FK)
            if (!$schedule) {
                $schedule = Schedule::where('teacher_allocation_id', $allocationId)->first();
            }

            // Simpan per siswa
            foreach ($request->absensi as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        // KUNCI UNIK: Kombinasi Teacher Alloc, Siswa, & Tanggal
                        'teacher_allocation_id' => $allocationId,
                        'student_id'            => $studentId,
                        'date'                  => $tanggal,
                    ],
                    [
                        'schedule_id' => $schedule ? $schedule->id : null, // Induk Jadwal
                        'status'      => $data['status'] ?? 'H',
                        'note'        => $data['note'] ?? null,
                    ]
                );
            }
        });

        // Redirect balik ke List Mapel dengan pesan sukses
        // Kita butuh classroom_id untuk redirect, ambil dari request (dikirim hidden di form)
        $classroomId = $request->classroom_id;

        return redirect()->route('guru.absensi.mapel', $classroomId)
            ->with('success', 'Data absensi berhasil disimpan!');
    }

    // =========================================================================
    // 5. HALAMAN REKAPITULASI (Report Card)
    // =========================================================================
    public function rekap($allocation_id)
    {
        $allocation = TeacherAllocation::with(['classroom', 'subject'])->findOrFail($allocation_id);
        $students = Student::where('classroom_id', $allocation->classroom_id)->orderBy('nama_lengkap')->get();

        // Ambil semua data absen mapel ini
        $allAttendances = Attendance::where('teacher_allocation_id', $allocation_id)->get();

        // A. Hitung Statistik Per Siswa
        $recapData = [];
        $totalPertemuan = $allAttendances->unique('date')->count();

        foreach ($students as $student) {
            $myAbsen = $allAttendances->where('student_id', $student->id);

            $h = $myAbsen->where('status', 'H')->count();
            $s = $myAbsen->where('status', 'S')->count();
            $i = $myAbsen->where('status', 'I')->count();
            $a = $myAbsen->where('status', 'A')->count();

            // Rumus Persentase: (Hadir / Total Pertemuan) * 100
            $percent = ($totalPertemuan > 0) ? ($h / $totalPertemuan) * 100 : 0;

            $recapData[] = [
                'student' => $student,
                'H' => $h, 'S' => $s, 'I' => $i, 'A' => $a,
                'percent' => $percent
            ];
        }

        // B. Data History (Riwayat Tanggal)
        $historyDates = Attendance::where('teacher_allocation_id', $allocation_id)
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($item) use ($allocation_id) {
                $item->total_hadir = Attendance::where('teacher_allocation_id', $allocation_id)
                    ->where('date', $item->date)
                    ->where('status', 'H')
                    ->count();
                return $item;
            });

        return view('guru.absensi.recap', compact('allocation', 'recapData', 'totalPertemuan', 'historyDates'));
    }

    // =========================================================================
    // 6. HAPUS DATA (Delete History)
    // =========================================================================
    public function destroy($allocation_id, $date)
    {
        $deleted = Attendance::where('teacher_allocation_id', $allocation_id)
            ->whereDate('date', $date)
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Data absensi tanggal ' . Carbon::parse($date)->format('d F Y') . ' berhasil dihapus.');
        } else {
            return back()->with('error', 'Data tidak ditemukan.');
        }
    }
    // =========================================================================
    // 7. HALAMAN JURNAL / RIWAYAT (Khusus Cek & Edit Pertemuan)
    // =========================================================================
    public function jurnal($allocation_id)
    {
        $allocation = TeacherAllocation::with(['classroom', 'subject'])->findOrFail($allocation_id);

        // UBAH DISINI: Ganti 'desc' jadi 'asc' biar urut dari tanggal tua ke muda
        $riwayat = Attendance::where('teacher_allocation_id', $allocation_id)
            ->select('date')
            ->selectRaw("COUNT(CASE WHEN status = 'H' THEN 1 END) as hadir")
            ->selectRaw("COUNT(CASE WHEN status = 'S' THEN 1 END) as sakit")
            ->selectRaw("COUNT(CASE WHEN status = 'I' THEN 1 END) as izin")
            ->selectRaw("COUNT(CASE WHEN status = 'A' THEN 1 END) as alpha")
            ->groupBy('date')
            ->orderBy('date', 'asc') // <--- INI KUNCINYA (ASCENDING)
            ->get();

        return view('guru.absensi.jurnal', compact('allocation', 'riwayat'));
    }
}
