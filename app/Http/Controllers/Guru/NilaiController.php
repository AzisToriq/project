<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\TeacherAllocation;
use App\Models\Student;
use App\Models\Grade;

class NilaiController extends Controller
{
    // ==========================================
    // 1. HALAMAN DEPAN (DAFTAR KELAS)
    // ==========================================
    public function index()
    {
        $user = Auth::user();

        // Pastikan user ini terdaftar sebagai Guru
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Ambil alokasi (mapel & kelas) yang diajar guru ini
        $allocations = TeacherAllocation::with(['classroom', 'subject'])
                        ->where('teacher_id', $teacher->id)
                        ->get();

        return view('guru.nilai.index', compact('allocations'));
    }

    // ==========================================
    // 2. FORM INPUT & EDIT NILAI
    // ==========================================
    public function create($allocation_id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Validasi: Pastikan mapel ini beneran diajar oleh guru yang login
        $allocation = TeacherAllocation::where('id', $allocation_id)
                        ->where('teacher_id', $teacher->id)
                        ->with(['classroom', 'subject'])
                        ->firstOrFail();

        // Ambil Siswa di kelas tersebut (Urut Abjad)
        $students = Student::where('classroom_id', $allocation->classroom_id)
                        ->orderBy('nama_lengkap', 'asc')
                        ->get();

        // Ambil Nilai yang sudah ada (biar form terisi kalau mau edit)
        $existingGrades = Grade::where('teacher_allocation_id', $allocation_id)->get();

        // Format data nilai biar mudah dipanggil di view: $grades[student_id][type]
        // Contoh: $grades[10][UH1] = 85
        $grades = [];
        foreach ($existingGrades as $g) {
            $grades[$g->student_id][$g->type] = $g->score;
        }

        return view('guru.nilai.create', compact('allocation', 'students', 'grades'));
    }

    // ==========================================
    // 3. SIMPAN & UPDATE NILAI (LOGIKA UTAMA)
    // ==========================================
    public function store(Request $request, $allocation_id)
    {
        // 1. Validasi Guru & Allocation
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $allocation = TeacherAllocation::where('id', $allocation_id)
                        ->where('teacher_id', $teacher->id)
                        ->firstOrFail();

        // 2. Validasi Input Array
        $request->validate([
            'nilai'       => 'required|array',
            'nilai.*.UH1' => 'nullable|numeric|min:0|max:100',
            'nilai.*.UH2' => 'nullable|numeric|min:0|max:100',
            'nilai.*.UTS' => 'nullable|numeric|min:0|max:100',
            'nilai.*.UAS' => 'nullable|numeric|min:0|max:100',
        ]);

        $inputs = $request->input('nilai');

        if (!$inputs) {
            return back()->with('success', 'Tidak ada data nilai yang disimpan.');
        }

        // 3. Loop Simpan / Hapus
        foreach ($inputs as $studentId => $scores) {
            foreach ($scores as $type => $score) {

                // LOGIKA PENTING:
                // Cek apakah score TIDAK NULL dan TIDAK KOSONG STRING ('')
                // ($score !== null && $score !== '') memastikan angka 0 tetap dianggap ADA.

                if ($score !== null && $score !== '') {
                    // KASUS A: ADA ISI (Termasuk angka 0) -> SIMPAN / UPDATE
                    Grade::updateOrCreate(
                        [
                            'teacher_allocation_id' => $allocation_id,
                            'student_id'            => $studentId,
                            'type'                  => $type // 'UH1', 'UH2', 'UTS', 'UAS'
                        ],
                        [
                            'score' => $score
                        ]
                    );
                } else {
                    // KASUS B: KOSONG (User menghapus nilai) -> HAPUS DARI DATABASE
                    // Ini memungkinkan user mengosongkan nilai yang salah input
                    Grade::where([
                        'teacher_allocation_id' => $allocation_id,
                        'student_id'            => $studentId,
                        'type'                  => $type
                    ])->delete();
                }
            }
        }

        // Return back() agar Guru tetap di halaman input (UX lebih baik untuk edit massal)
        return back()->with('success', 'Nilai berhasil disimpan dan diperbarui!');
    }

}
