<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Import Model
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\TeacherAllocation;

class ScheduleController extends Controller
{
    /**
     * 1. INDEX: Menampilkan Daftar Kelas (Direktori)
     */
    public function index()
    {
        // Ambil semua kelas untuk ditampilkan sebagai kartu/folder
        // Diurutkan berdasarkan Level (1-6) lalu Nama Kelas (A,B,C)
        $classrooms = Classroom::orderBy('level')->orderBy('nama_kelas')->get();

        return view('admin.schedule.index', compact('classrooms'));
    }

    /**
     * 2. SHOW: Menampilkan Detail Jadwal Spesifik per Kelas
     */
    public function show($id)
    {
        // A. Kelas yang sedang dibuka
        $classroom = Classroom::findOrFail($id);

        // B. Daftar semua kelas (Untuk Dropdown "Ganti Kelas" di Header)
        $allClassrooms = Classroom::orderBy('level')->orderBy('nama_kelas')->get();

        // C. Ambil Jadwal kelas ini
        // Kita cari jadwal yang teacher_allocation-nya milik kelas ini
        $schedules = Schedule::whereHas('teacher_allocation', function($q) use ($id) {
                $q->where('classroom_id', $id);
            })
            ->with(['teacher_allocation.subject', 'teacher_allocation.teacher']) // Eager Load agar query ringan
            ->orderBy('start_time', 'asc') // Urutkan jam mulai (Pagi -> Siang)
            ->get();

        // Grouping per Hari (Senin, Selasa, dst) untuk tampilan Grid
        $groupedSchedule = $schedules->groupBy('day');

        // D. Data Plotting Guru (Untuk isi Dropdown di Modal Tambah Jadwal)
        // Kita butuh daftar Mapel & Guru yang sudah di-plot di kelas ini saja
        $allocations = TeacherAllocation::where('classroom_id', $id)
                        ->with(['teacher', 'subject'])
                        ->get();

        return view('admin.schedule.show', compact('classroom', 'allClassrooms', 'groupedSchedule', 'allocations'));
    }

    /**
     * 3. STORE: Simpan Jadwal Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_allocation_id' => 'required|exists:teacher_allocations,id',
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time', // Validasi: Jam selesai harus setelah mulai
        ]);

        // Simpan Data
        // Kita gunakan $request->all() karena field di form sudah sesuai dengan kolom DB
        Schedule::create($request->all());

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * 4. UPDATE: Edit Jam & Hari (Fitur Baru)
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        // Update Data (Hanya Hari & Jam)
        // Mapel tidak diubah di sini (harus hapus buat baru jika ganti mapel)
        $schedule->update([
            'day'        => $request->day,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
        ]);

        return back()->with('success', 'Jam pelajaran berhasil diperbarui!');
    }

    /**
     * 5. DESTROY: Hapus Jadwal
     */
    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return back()->with('success', 'Jadwal berhasil dihapus!');
    }
}
