<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // Kita gunakan guarded id agar lebih fleksibel (semua kolom selain id boleh diisi)
    // Ini menghindari error "Mass assignment" jika kita lupa nambah kolom di $fillable
    protected $guarded = ['id'];

    // Cast agar start_time & end_time otomatis jadi objek Carbon (mudah diformat)
    protected $casts = [
        'start_time' => 'datetime', // atau 'string' jika ingin mentah
        'end_time'   => 'datetime',
    ];

    // ==========================================
    // 1. RELASI UTAMA (WAJIB ADA)
    // ==========================================
    // Karena jadwal terhubung ke plotting guru, ini relasi paling penting.
    public function teacher_allocation()
    {
        return $this->belongsTo(TeacherAllocation::class, 'teacher_allocation_id');
    }

    // ==========================================
    // 2. HELPER RELATIONS (OPSIONAL)
    // ==========================================
    // Fungsi ini memudahkan kita mengambil data mapel/guru langsung dari jadwal
    // tanpa harus menulis $schedule->teacher_allocation->subject

    // Cara panggil: $schedule->subject
    public function subject()
    {
        return $this->hasOneThrough(
            Subject::class,
            TeacherAllocation::class,
            'id', // Foreign key di TeacherAllocation
            'id', // Foreign key di Subject
            'teacher_allocation_id', // Local key di Schedule
            'subject_id' // Local key di TeacherAllocation
        );
    }

    // Cara panggil: $schedule->teacher
    public function teacher()
    {
        return $this->hasOneThrough(
            Teacher::class,
            TeacherAllocation::class,
            'id',
            'id',
            'teacher_allocation_id',
            'teacher_id'
        );
    }

    // Cara panggil: $schedule->classroom
    public function classroom()
    {
        return $this->hasOneThrough(
            Classroom::class,
            TeacherAllocation::class,
            'id',
            'id',
            'teacher_allocation_id',
            'classroom_id'
        );
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
