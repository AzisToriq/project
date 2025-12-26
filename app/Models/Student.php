<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    // HAPUS SEMUA TRAIT API/AUTH. Cukup Model biasa.

    protected $fillable = [
        'user_id',          // Link ke Akun Login
        'parent_user_id',   // Link ke Akun Ortu
        'classroom_id',     // Link ke Kelas
        'nisn',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'nik',
        'nomor_telepon',
        'email',
        'alamat',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'dusun',
        'kode_pos',
        'tahun_masuk',
        'status_aktif',
        'nama_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'nomor_telepon_ortu',
    ];

    // --- RELASI ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    // Relasi ke SPP (jika ada)
    public function sppPayments()
    {
        return $this->hasMany(SppPayment::class);
    }

    // Relasi ke Absensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relasi ke Nilai
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // Relasi ke Pengumpulan Tugas
    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // Helper Nama Kelas Lengkap
    public function getNamaKelasLengkapAttribute(): ?string
    {
        if ($this->classroom) {
            // Sesuaikan dengan nama kolom di tabel classrooms lu (nama_kelas/name)
            return "Kelas " . ($this->classroom->nama_kelas ?? $this->classroom->name);
        }
        return null;
    }
}
