<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <--- WAJIB ADA agar tidak error 'Undefined type'

// Import Models
use App\Models\Assignment;
use App\Models\TeacherAllocation;
use App\Models\Student;
use App\Models\AssignmentSubmission;
use App\Models\Teacher;

class AssignmentController extends Controller
{
    // ==========================================
    // 1. DAFTAR TUGAS (INDEX)
    // ==========================================
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            $assignments = Assignment::with(['teacher', 'classroom', 'subject'])->latest()->get();
            $allocations = TeacherAllocation::with(['classroom', 'subject', 'teacher'])->get();
        } else {
            $teacher = Teacher::where('user_id', $user->id)->first();

            // Tugas yang dibuat guru ini
            $assignments = Assignment::where('teacher_id', $teacher->id)
                ->with(['classroom', 'subject'])
                ->latest()
                ->get();

            // Alokasi mengajar untuk dropdown pilihan kelas
            $allocations = TeacherAllocation::where('teacher_id', $teacher->id)
                ->with(['classroom', 'subject'])
                ->get();
        }

        return view('guru.tugas.index', compact('assignments', 'allocations'));
    }

    // ==========================================
    // 2. FORM BUAT TUGAS (CREATE)
    // ==========================================
    public function create(Request $request)
    {
        $user = Auth::user();
        $selected_allocation_id = $request->query('allocation_id');

        if ($user->role == 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $allocations = TeacherAllocation::with(['classroom', 'subject'])
                            ->where('teacher_id', $teacher->id)
                            ->get();

            if ($allocations->isEmpty()) {
                return redirect()->route('guru.tugas.index')->with('error', 'Belum ada jadwal mengajar.');
            }
        } else {
            $allocations = TeacherAllocation::with(['classroom', 'subject', 'teacher'])->get();
        }

        return view('guru.tugas.create', compact('allocations', 'selected_allocation_id'));
    }

    // ==========================================
    // 3. SIMPAN TUGAS (STORE)
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'teacher_allocation_id' => 'required|exists:teacher_allocations,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'deadline'     => 'required|date',
            'type'         => 'required|in:online,offline',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,jpg,png,zip|max:5120'
        ]);

        $allocation = TeacherAllocation::findOrFail($request->teacher_allocation_id);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create([
            'teacher_id'   => $allocation->teacher_id,
            'classroom_id' => $allocation->classroom_id,
            'subject_id'   => $allocation->subject_id,
            'title'        => $request->title,
            'description'  => $request->description,
            'deadline'     => $request->deadline,
            'type'         => $request->type,
            'file_path'    => $filePath, // Simpan path file jika ada
        ]);

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil diterbitkan!');
    }

    // ==========================================
    // 4. SHOW (LIHAT TUGAS & PENGUMPULAN SISWA)
    // ==========================================
    public function show($id)
    {
        $assignment = Assignment::with(['classroom', 'subject', 'teacher'])->findOrFail($id);

        // Ambil semua siswa di kelas tersebut agar yang belum mengumpulkan tetap terlihat
        $students = Student::where('classroom_id', $assignment->classroom_id)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $submissions = AssignmentSubmission::where('assignment_id', $id)
            ->get()
            ->keyBy('student_id');

        return view('guru.tugas.show', compact('assignment', 'students', 'submissions'));
    }

    // ==========================================
    // 5. UPDATE NILAI & FEEDBACK SISWA
    // ==========================================
    public function updateGrade(Request $request, $submission_id)
    {
        $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string'
        ]);

        $submission = AssignmentSubmission::findOrFail($submission_id);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback
        ]);

        return back()->with('success', 'Nilai dan feedback berhasil disimpan!');
    }

    // ==========================================
    // 6. DELETE TUGAS
    // ==========================================
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);

        // Hapus file fisik dari storage jika ada
        if ($assignment->file_path && Storage::disk('public')->exists($assignment->file_path)) {
            Storage::disk('public')->delete($assignment->file_path);
        }

        $assignment->delete();
        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil dihapus!');
    }

    // ==========================================
    // 7. FORM EDIT TUGAS
    // ==========================================
    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        $user = Auth::user();

        if ($user->role == 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($assignment->teacher_id != $teacher->id) {
                abort(403, 'Akses Ditolak: Anda bukan pembuat tugas ini.');
            }

            $allocations = TeacherAllocation::with(['classroom', 'subject'])
                            ->where('teacher_id', $teacher->id)
                            ->get();
        } else {
            $allocations = TeacherAllocation::with(['classroom', 'subject', 'teacher'])->get();
        }

        return view('guru.tugas.edit', compact('assignment', 'allocations'));
    }

    // ==========================================
    // 8. UPDATE DATA TUGAS
    // ==========================================
    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);

        $request->validate([
            'teacher_allocation_id' => 'required|exists:teacher_allocations,id',
            'title' => 'required|string|max:255',
            'deadline' => 'required',
            'type' => 'required|in:online,offline',
            'file' => 'nullable|file|max:5120'
        ]);

        $allocation = TeacherAllocation::findOrFail($request->teacher_allocation_id);

        $data = [
            'teacher_id'   => $allocation->teacher_id,
            'classroom_id' => $allocation->classroom_id,
            'subject_id'   => $allocation->subject_id,
            'title'        => $request->title,
            'description'  => $request->description,
            'deadline'     => $request->deadline,
            'type'         => $request->type,
        ];

        // Logic File: Hanya ganti jika user upload file baru
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($assignment->file_path && Storage::disk('public')->exists($assignment->file_path)) {
                Storage::disk('public')->delete($assignment->file_path);
            }

            // Simpan file baru
            $data['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $assignment->update($data);

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil diperbarui!');
    }
}
