<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// Import Semua Model
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\TeacherAllocation;
use App\Models\Schedule;
use App\Models\Grade;

class DataMasterController extends Controller
{
    // ==========================================
    // 1. SUBJECTS (MATA PELAJARAN)
    // ==========================================
    public function indexSubjects()
    {
        $subjects = Subject::orderBy('nama_mapel', 'asc')->get();
        return view('admin.datamaster.subjects.index', compact('subjects'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code'
        ]);

        Subject::create([
            'nama_mapel' => $request->nama_mapel,
            'code' => $request->code
        ]);

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan');
    }

    public function updateSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'code' => ['required', Rule::unique('subjects')->ignore($subject->id)],
        ]);

        $subject->update([
            'nama_mapel' => $request->nama_mapel,
            'code' => $request->code
        ]);
        return back()->with('success', 'Mata Pelajaran berhasil diupdate');
    }

    public function destroySubject($id)
    {
        Subject::findOrFail($id)->delete();
        return back()->with('success', 'Mata Pelajaran berhasil dihapus');
    }

    // ==========================================
    // 2. CLASSROOMS (DATA KELAS)
    // ==========================================
    public function indexClassrooms()
    {
        $classrooms = Classroom::with('waliKelas')->orderBy('level')->orderBy('nama_kelas')->get();
        $teachers = Teacher::all(); // Untuk dropdown wali kelas
        return view('admin.datamaster.classrooms.index', compact('classrooms', 'teachers'));
    }

    public function storeClassroom(Request $request)
    {
        $request->validate(['nama_kelas' => 'required', 'level' => 'required']);

        Classroom::create([
            'nama_kelas' => $request->nama_kelas,
            'level' => $request->level,
            'major' => $request->major ?? 'Umum',
            'academic_year' => $request->academic_year ?? '2025/2026',
            'wali_kelas_id' => $request->wali_kelas_id
        ]);
        return back()->with('success', 'Kelas berhasil ditambahkan');
    }

    public function updateClassroom(Request $request, $id)
    {
        Classroom::findOrFail($id)->update($request->all());
        return back()->with('success', 'Kelas berhasil diupdate');
    }

    public function destroyClassroom($id)
    {
        Classroom::findOrFail($id)->delete();
        return back()->with('success', 'Kelas berhasil dihapus');
    }

    // ==========================================
    // 3. STUDENTS (DATA SISWA LENGKAP)
    // ==========================================
    public function indexStudents()
    {
        // Ambil data siswa dengan relasi yang dibutuhkan
        // Tambahan: Ambil list Parent untuk dropdown di modal
        $parents = User::where('role', 'parent')->get();
        $classrooms = Classroom::withCount('students')->orderBy('level')->orderBy('nama_kelas')->get();

        return view('admin.datamaster.students.index', compact('classrooms', 'parents'));
    }

    public function showStudentsByClass($class_id)
    {
        // Ambil list Parent juga disini jika modal edit ada di halaman detail
        $parents = User::where('role', 'parent')->get();
        $classroom = Classroom::findOrFail($class_id);

        $students = Student::where('classroom_id', $class_id)
                    ->with(['user', 'parent']) // Load relasi Parent User ID
                    ->orderBy('nama_lengkap')
                    ->get();

        return view('admin.datamaster.students.list', compact('classroom', 'students', 'parents'));
    }

    public function storeStudent(Request $request)
    {
        // 1. Validasi Input Wajib
        $request->validate([
            'nama_lengkap' => 'required',
            'classroom_id' => 'required',
            'nisn'         => 'required|unique:students,nisn|unique:users,username',
            'email'        => 'nullable|email',
            // 'parent_user_id' => 'nullable|exists:users,id', // Opsional
        ]);

        // --- TRANSACTION START ---
        DB::transaction(function () use ($request) {

            // 2. Buat User Siswa (Login)
            $user = User::create([
                'name'      => $request->nama_lengkap,
                'username'  => $request->nisn, // Login pakai NISN
                'email'     => $request->email ?? $request->nisn . '@student.sekolah.id', // Dummy email unik jika kosong
                'password'  => Hash::make('password123'), // Default Password
                'role'      => 'student',
                'is_active' => true,
            ]);

            // 3. Buat Data Siswa Lengkap
            Student::create([
                'user_id'       => $user->id,
                'classroom_id'  => $request->classroom_id,
                'parent_user_id'=> $request->parent_user_id, // <--- INTEGRASI ORTU

                // Data Pribadi
                'nama_lengkap'  => $request->nama_lengkap,
                'nisn'          => $request->nisn,
                'nis'           => $request->nis,
                'jenis_kelamin' => $request->jenis_kelamin ?? 'L',
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama'         => $request->agama,
                'nik'           => $request->nik,
                'nomor_telepon' => $request->nomor_telepon,
                'email'         => $request->email,

                // Alamat
                'alamat'        => $request->alamat,
                'provinsi'      => $request->provinsi ?? 'Lampung',
                'kabupaten'     => $request->kabupaten,
                'kecamatan'     => $request->kecamatan,
                'kode_pos'      => $request->kode_pos,

                // Data Orang Tua (Biodata Text)
                'nama_ayah'      => $request->nama_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'nama_ibu'       => $request->nama_ibu,
                'pekerjaan_ibu'  => $request->pekerjaan_ibu,

                // Status
                'status_aktif'   => 'Aktif',
            ]);
        });
        // --- TRANSACTION END ---

        return back()->with('success', 'Data Siswa lengkap & Akun Login berhasil dibuat!');
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required',
            'nisn'         => 'required|unique:students,nisn,'.$id,
        ]);

        DB::transaction(function () use ($request, $student) {

            // 1. Update Data Siswa (Termasuk parent_user_id)
            // Kita gunakan except() untuk membuang field yg tidak ada di tabel students
            $student->update($request->except(['_token', '_method', 'password', 'reset_password']));

            // 2. Sinkronisasi User Login
            if($student->user) {
                $userData = [
                    'name'     => $request->nama_lengkap,
                    'username' => $request->nisn, // Kalau NISN berubah, login juga berubah
                    'email'    => $request->email ?? $student->user->email,
                ];

                // Fitur Reset Password Manual (Dari Checkbox Edit)
                if ($request->has('reset_password') && $request->reset_password == '1') {
                    $userData['password'] = Hash::make('password123');
                }

                $student->user->update($userData);
            }
        });

        return back()->with('success', 'Data siswa & akun berhasil diperbarui!');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);

        // Hapus Usernya juga biar bersih
        if($student->user_id) {
            User::destroy($student->user_id);
        }

        $student->delete();
        return back()->with('success', 'Siswa dan Akun Login berhasil dihapus');
    }

    // ==========================================
    // 4. TEACHERS (DATA GURU)
    // ==========================================
    public function indexTeachers(Request $request)
    {
        // Query Dasar
        $query = Teacher::with('user');

        // 1. Fitur Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 2. Fitur Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Urutkan dan Ambil Data
        $teachers = $query->orderBy('nama_lengkap', 'asc')->get();

        // AJAX Response untuk Live Search
        if ($request->ajax()) {
            return view('admin.datamaster.teachers.table', compact('teachers'))->render();
        }

        return view('admin.datamaster.teachers.index', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        // 1. VALIDASI LENGKAP
        $request->validate([
            'nip'           => 'required|unique:teachers,nip|unique:users,username',
            'nama_lengkap'  => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'jenis_kelamin' => 'required',
            'tempat_lahir'  => 'required',
            'tanggal_lahir' => 'required|date',
            'agama'         => 'required',
            'nik'           => 'required|numeric|unique:teachers,nik',
            'nomor_telepon' => 'required',
            'alamat'        => 'required',
            'provinsi'      => 'required',
            'kabupaten'     => 'required',
            'kecamatan'     => 'required',
            'kelurahan'     => 'required',
            'dusun'         => 'nullable',
            'kode_pos'      => 'required',
            'jabatan'       => 'required',
            'status'        => 'required',
            'tanggal_masuk' => 'required|date',
        ]);

        // 2. PROSES SIMPAN (TRANSACTION)
        DB::transaction(function () use ($request) {

            // A. Buat Akun Login
            $user = User::create([
                'name'      => $request->nama_lengkap,
                'username'  => $request->nip,
                'email'     => $request->email,
                'password'  => Hash::make('password123'),
                'role'      => 'teacher',
                'is_active' => true,
            ]);

            // B. Buat Profil Guru
            Teacher::create([
                'user_id'       => $user->id,
                'nip'           => $request->nip,
                'nama_lengkap'  => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama'         => $request->agama,
                'nik'           => $request->nik,
                'nomor_telepon' => $request->nomor_telepon,
                'email'         => $request->email,
                'alamat'        => $request->alamat,
                'provinsi'      => $request->provinsi,
                'kabupaten'     => $request->kabupaten,
                'kecamatan'     => $request->kecamatan,
                'kelurahan'     => $request->kelurahan,
                'dusun'         => $request->dusun,
                'kode_pos'      => $request->kode_pos,
                'jabatan'       => $request->jabatan,
                'status'        => $request->status,
                'tanggal_masuk' => $request->tanggal_masuk,
            ]);
        });

        return back()->with('success', 'Guru berhasil ditambahkan dan Akun Login telah dibuat!');
    }

    public function updateTeacher(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        // 1. VALIDASI UPDATE
        $validated = $request->validate([
            'nip'           => 'required|unique:teachers,nip,' . $id,
            'nama_lengkap'  => 'required|string|max:100',
            'email'         => 'required|email',
            'jenis_kelamin' => 'required',
            'tempat_lahir'  => 'required',
            'tanggal_lahir' => 'required|date',
            'agama'         => 'required',
            'nik'           => 'required|numeric|unique:teachers,nik,' . $id,
            'nomor_telepon' => 'required',
            'alamat'        => 'required',
            'provinsi'      => 'required',
            'kabupaten'     => 'required',
            'kecamatan'     => 'required',
            'kelurahan'     => 'required',
            'dusun'         => 'nullable',
            'kode_pos'      => 'required',
            'jabatan'       => 'required',
            'status'        => 'required',
            'tanggal_masuk' => 'required|date',
            'password'      => 'nullable|string|min:6', // Validasi Password jika diisi
        ]);

        // 2. PROSES UPDATE SINKRON
        DB::transaction(function () use ($request, $teacher, $validated) {

            // A. Update Profil Teacher
            $teacherData = collect($validated)->except(['password', 'is_active'])->toArray();
            $teacher->update($teacherData);

            // B. Update Akun User
            if ($teacher->user) {
                $userData = [
                    'name'     => $request->nama_lengkap,
                    'username' => $request->nip,
                    'email'    => $request->email,
                ];

                if ($request->has('is_active')) {
                    $userData['is_active'] = $request->is_active;
                }

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $teacher->user->update($userData);
            }
        });

        return back()->with('success', 'Data Guru dan Akun Login berhasil diperbarui!');
    }

    public function destroyTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);

        try {
            DB::transaction(function () use ($teacher) {
                // 1. Hapus Relasi
                TeacherAllocation::where('teacher_id', $teacher->id)->delete();
                Classroom::where('wali_kelas_id', $teacher->id)->update(['wali_kelas_id' => null]);

                // 2. Hapus User
                if($teacher->user_id) {
                    User::destroy($teacher->user_id);
                }

                // 3. Hapus Data Guru
                $teacher->delete();
            });

            return back()->with('success', 'Guru dan Akun Login berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus guru. Pastikan tidak ada data terkait.');
        }
    }

    // ==========================================
    // 5. PLOTTING GURU (ALLOCATIONS)
    // ==========================================
    public function indexAllocations()
    {
        $classrooms = Classroom::with(['teacher_allocations.teacher', 'teacher_allocations.subject'])
                                ->orderBy('level')
                                ->orderBy('nama_kelas')
                                ->get();

        $teachers = Teacher::orderBy('nama_lengkap')->get();
        $subjects = Subject::orderBy('nama_mapel')->get();

        return view('admin.datamaster.allocations.index', compact('classrooms', 'teachers', 'subjects'));
    }

    public function storeAllocation(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'classroom_id' => 'required',
            'subject_id' => 'required',
        ]);

        // Cek Duplikat
        $exists = TeacherAllocation::where('teacher_id', $request->teacher_id)
                    ->where('classroom_id', $request->classroom_id)
                    ->where('subject_id', $request->subject_id)
                    ->exists();

        if($exists) {
            return back()->with('error', 'Guru tersebut sudah mengajar mapel ini di kelas ini!');
        }

        TeacherAllocation::create([
            'teacher_id' => $request->teacher_id,
            'classroom_id' => $request->classroom_id,
            'subject_id' => $request->subject_id,
            'semester' => 'ganjil',
            'academic_year' => '2025/2026'
        ]);

        return back()->with('success', 'Plotting guru berhasil ditambahkan!');
    }

    public function destroyAllocation($id)
    {
        TeacherAllocation::findOrFail($id)->delete();
        return back()->with('success', 'Plotting berhasil dihapus!');
    }

    // ==========================================
    // 6. PREVIEW JADWAL (Admin View)
    // ==========================================
    public function previewJadwalKelas($class_id)
    {
        $classroom = Classroom::findOrFail($class_id);

        $schedules = Schedule::whereHas('teacher_allocation', function($q) use ($class_id) {
                $q->where('classroom_id', $class_id);
            })
            ->with(['teacher_allocation.subject', 'teacher_allocation.teacher'])
            ->orderBy('start_time', 'asc')
            ->get();

        $groupedSchedule = $schedules->groupBy('day');
        $daysOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.datamaster.classrooms.jadwal', compact('classroom', 'groupedSchedule', 'daysOrder'));
    }

    // ==========================================
    // 7. RAPOR SISWA (Admin View)
    // ==========================================
    public function showStudentRapor($id)
    {
        $student = Student::with(['classroom.waliKelas', 'user'])->findOrFail($id);

        $allocations = TeacherAllocation::with(['subject', 'teacher.user'])
                        ->where('classroom_id', $student->classroom_id)
                        ->get();

        $rawGrades = Grade::where('student_id', $student->id)->get();

        $grades = [];
        foreach($rawGrades as $g) {
            $type = strtolower($g->type);
            $grades[$g->teacher_allocation_id][$type] = $g->score;
        }

        // === DATA DUMMY (MANUAL) AGAR TIDAK ERROR SAAT VIEW DIPANGGIL ===
        $absensi = [
            'sakit' => 0,
            'izin'  => 0,
            'alpha' => 0
        ];

        $ekskul = [
            [
                'nama'       => 'Pramuka (Wajib)',
                'predikat'   => 'B',
                'keterangan' => 'Aktif mengikuti kegiatan.'
            ]
        ];

        return view('admin.datamaster.students.rapor', compact('student', 'allocations', 'grades', 'absensi', 'ekskul'));
    }

    // ==========================================
    // 8. FITUR TAMBAHAN (TOGGLE RAPOR)
    // ==========================================
    public function toggleRaporStatus($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->is_rapor_published = !$classroom->is_rapor_published;
        $classroom->save();

        $status = $classroom->is_rapor_published ? 'DIBUKA (Dapat dilihat siswa)' : 'DITUTUP (Disembunyikan dari siswa)';

        return back()->with('success', "Akses Rapor untuk kelas {$classroom->nama_kelas} berhasil {$status}.");
    }

    // ==========================================
    // 9. PARENTS (DATA ORANG TUA / WALI)
    // ==========================================

    // HALAMAN 1: PILIH KELAS
    public function indexParents()
    {
        // Kita ambil data kelas, hitung jumlah siswa yg punya ortu di kelas itu (opsional)
        $classrooms = Classroom::orderBy('level')
                        ->orderBy('nama_kelas')
                        ->withCount('students')
                        ->get();

        // Hitung ortu yang belum terhubung ke siswa manapun (Opsional, buat info aja)
        $unlinked_parents = User::where('role', 'parent')
                                ->doesntHave('students')
                                ->count();

        return view('admin.datamaster.parents.index', compact('classrooms', 'unlinked_parents'));
    }

    // HALAMAN 2: LIST ORANG TUA PER KELAS
    public function showParentsByClass($class_id)
    {
        $classroom = Classroom::findOrFail($class_id);

        // Ambil User (Parent) yang punya anak (Student) di kelas ini
        $parents = User::where('role', 'parent')
                    ->whereHas('students', function($q) use ($class_id) {
                        $q->where('classroom_id', $class_id);
                    })
                    ->with(['students' => function($q) {
                        $q->with('classroom'); // Load data kelas anak
                    }])
                    ->orderBy('name', 'asc')
                    ->get();

        return view('admin.datamaster.parents.list', compact('classroom', 'parents'));
    }

    public function storeParent(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role'      => 'parent',
            'is_active' => true,
        ]);

        return back()->with('success', 'Akun Orang Tua berhasil dibuat.');
    }

    public function updateParent(Request $request, $id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($parent->id)],
            'username' => ['required', 'string', Rule::unique('users')->ignore($parent->id)],
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $request->username,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $parent->update($data);

        return back()->with('success', 'Data Orang Tua berhasil diperbarui.');
    }

    public function destroyParent($id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);

        // Cek apakah punya anak, kalau ada set null dulu di data siswa (sudah dihandle migration onDelete set null sih, tapi buat safety logic)
        $parent->delete();

        return back()->with('success', 'Akun Orang Tua berhasil dihapus.');
    }
}
