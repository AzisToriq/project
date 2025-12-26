<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Casting agar deadline otomatis jadi Carbon (enak diolah tanggalnya)
    protected $casts = [
        'deadline' => 'datetime',
    ];

    // --- RELASI KE DATABASE BARU (CAPSTONE STYLE) ---

    // Tugas ini milik Guru siapa?
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Tugas ini untuk Kelas mana?
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Tugas ini pelajaran apa?
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // --- RELASI KE PENGUMPULAN TUGAS (REQUEST KAMU) ---
    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }
}
