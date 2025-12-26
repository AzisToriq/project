<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAllocation extends Model
{
    use HasFactory;

    // Guarded ID agar mass assignment aman
    protected $guarded = ['id'];

    // ==========================================
    // 1. RELASI KE DATA UTAMA (PARENTS)
    // ==========================================

    // Relasi ke Guru yang mengajar
    // Akses: $allocation->teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // Relasi ke Kelas tempat mengajar
    // Akses: $allocation->classroom
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    // Relasi ke Mata Pelajaran
    // Akses: $allocation->subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // ==========================================
    // 2. RELASI KE DATA TURUNAN (CHILDREN)
    // ==========================================

    // Relasi ke Jadwal
    // Akses: $allocation->schedules
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_allocation_id');
    }

    // Relasi ke Nilai
    // Akses: $allocation->grades
    public function grades()
    {
        return $this->hasMany(Grade::class, 'teacher_allocation_id');
    }

    // Relasi ke Absensi
    // Akses: $allocation->attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'teacher_allocation_id');
    }
}
