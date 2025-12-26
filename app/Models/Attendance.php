<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // KITA PAKAI FILLABLE (WHITELIST)
    // Pastikan SEMUA kolom yang mau disimpan ada di sini.
    protected $fillable = [
        'schedule_id',            // <--- INI WAJIB ADA (Tadi error karena ini kurang)
        'student_id',
        'teacher_allocation_id',
        'date',
        'status',                 // H, S, I, A
        'note'
    ];

    // Relasi ke Siswa
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke Guru/Mapel
    public function teacher_allocation()
    {
        return $this->belongsTo(TeacherAllocation::class);
    }

    // Relasi ke Jadwal Spesifik (PENTING)
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
