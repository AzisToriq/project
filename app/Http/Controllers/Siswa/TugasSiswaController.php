<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Import Models
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;

class TugasSiswaController extends Controller
{
    // ==========================================
    // 1. DAFTAR TUGAS SISWA
    // ==========================================
    public function index()
{
    $user = Auth::user();
    $student = Student::where('user_id', $user->id)->firstOrFail();

    // AMBIL TUGAS DENGAN FILTER KEAMANAN
    $assignments = Assignment::where('classroom_id', $student->classroom_id)
        ->whereHas('subject') // <--- KUNCI KEAMANAN: Pastikan subject-nya ada
        ->whereHas('teacher') // <--- KUNCI KEAMANAN: Pastikan guru-nya ada
        ->with(['teacher', 'subject'])
        ->orderBy('deadline', 'asc')
        ->get();

    $submissions = AssignmentSubmission::where('student_id', $student->id)->get();

    return view('siswa.tugas.index', compact('assignments', 'submissions'));
}

    // ==========================================
    // 2. DETAIL TUGAS & FORM UPLOAD
    // ==========================================
    public function show($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Ambil Detail Tugas dengan Relasi
        $assignment = Assignment::with(['teacher', 'subject', 'classroom'])->findOrFail($id);

        // Security Check: Pastikan tugas ini memang untuk kelas siswa
        if ($assignment->classroom_id != $student->classroom_id) {
            abort(403, 'Tugas ini bukan untuk kelas Anda.');
        }

        // Cek apakah siswa sudah mengumpulkan sebelumnya?
        $submission = AssignmentSubmission::where('assignment_id', $id)
                        ->where('student_id', $student->id)
                        ->first();

        return view('siswa.tugas.show', compact('assignment', 'submission'));
    }

    // ==========================================
    // 3. PROSES SIMPAN / EDIT JAWABAN
    // ==========================================
    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        $assignment = Assignment::findOrFail($id);

        // 1. Validasi Input (Gunakan 'notes' sesuai DB)
        $rules = [
            'notes' => 'nullable|string|max:1000'
        ];

        // Cek submission lama
        $submission = AssignmentSubmission::where('assignment_id', $id)
                        ->where('student_id', $student->id)
                        ->first();

        if ($assignment->type == 'online') {
            // Jika belum ada submission, file wajib diisi. Jika sudah ada, file opsional (boleh tidak ganti).
            $rules['file'] = $submission ? 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:5120' : 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:5120';
        }

        $request->validate($rules);

        $filePath = $submission ? $submission->file_path : null;

        // 2. Proses Upload File Baru
        if ($request->hasFile('file')) {
            // Hapus file lama fisik jika ada
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            // Simpan file baru
            $filePath = $request->file('file')->store('tugas_siswa', 'public');
        }

        // 3. Simpan ke Database (Update or Create)
        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $id,
                'student_id' => $student->id
            ],
            [
                'file_path' => $filePath,
                'notes' => $request->notes, // Pakai 'notes' sesuai database kamu
                'updated_at' => now(),
            ]
        );

        return redirect()->route('siswa.tugas.index')->with('success', 'Tugas berhasil dikirim!');
    }

    // ==========================================
    // 4. BATALKAN PENGUMPULAN (DESTROY)
    // ==========================================
    public function destroy($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $submission = AssignmentSubmission::where('assignment_id', $id)
                        ->where('student_id', $student->id)
                        ->firstOrFail();

        // Security: Jangan hapus jika guru sudah memberi nilai
        if ($submission->grade !== null) {
            return back()->with('error', 'Tugas sudah dinilai oleh guru, tidak bisa dibatalkan.');
        }

        // Hapus file fisik
        if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
            Storage::disk('public')->delete($submission->file_path);
        }

        // Hapus record database
        $submission->delete();

        return redirect()->route('siswa.tugas.index')->with('success', 'Pengumpulan tugas berhasil dibatalkan.');
    }
}
