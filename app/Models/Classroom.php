<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    // Sesuaikan dengan Migration terakhir kita
    protected $fillable = [
        'nama_kelas',    // Contoh: "A", "B"
        'level',         // Contoh: 10, 11 (Pengganti 'tingkat')
        'major',         // Jurusan (IPA/IPS/Umum)
        'academic_year', // Tahun Ajaran
        'wali_kelas_id',
        'is_rapor_published', // <--- TAMBAHAN BARU (Untuk fitur Buka/Tutup Rapor)
    ];

    /**
     * CASTING TIPE DATA
     * Agar database 0/1 otomatis dibaca jadi true/false di Laravel
     */
    protected $casts = [
        'is_rapor_published' => 'boolean',
    ];

    /**
     * RELASI UTAMA (INI YANG BIKIN ERROR TADI)
     * Menghubungkan Kelas ke Plotting Guru (Mapel apa, Guru siapa)
     */
    public function teacher_allocations()
    {
        return $this->hasMany(TeacherAllocation::class);
    }

    /**
     * Relasi ke Wali Kelas
     */
    public function waliKelas()
    {
        return $this->belongsTo(Teacher::class, 'wali_kelas_id');
    }

    /**
     * Relasi ke Siswa
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Relasi ke Jadwal (Opsional, biasanya lewat allocations)
     * Tapi kita simpan aja buat jaga-jaga
     */
    public function schedules()
    {
        // Jadwal itu ada di tabel schedules, link-nya lewat teacher_allocations biasanya.
        // Tapi kalau di migration schedules ada classroom_id, pakai ini:
        return $this->hasMany(Schedule::class);
    }

    // --- SCOPES & ACCESSORS ---

    // Ganti 'tingkat' jadi 'level' sesuai database
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Nama Lengkap (Contoh: "10 - IPA A")
    public function getNamaLengkapAttribute(): string
    {
        return "Kelas {$this->level} - {$this->nama_kelas} " . ($this->major != 'Umum' ? $this->major : '');
    }
}
