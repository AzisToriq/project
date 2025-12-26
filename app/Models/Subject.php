<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mapel', // Nama asli di Database
        'code',
    ];

    // ==========================================
    // 1. SOLUSI AGAR TIDAK KOSONG (ACCESSOR)
    // ==========================================
    // Fungsi ini otomatis berjalan saat kamu panggil $subject->name
    // Dia akan mengoper nilai dari kolom 'nama_mapel'.
    public function getNameAttribute()
    {
        return $this->nama_mapel;
    }

    // ==========================================
    // 2. RELASI
    // ==========================================
    public function teacher_allocations()
    {
        return $this->hasMany(TeacherAllocation::class);
    }
}
