<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Teacher;

class JadwalGuruController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Cari data guru
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // 2. Ambil jadwal guru
        $schedules = Schedule::whereHas('teacher_allocation', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->with([
                'teacher_allocation.subject',
                'teacher_allocation.classroom'
            ])
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('start_time', 'asc')
            ->get();

        return view('guru.jadwal.index', compact('schedules', 'teacher'));
    }
}
