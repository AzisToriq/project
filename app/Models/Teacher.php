<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    // Pastikan semua kolom ini ada di database
    protected $fillable = [
        'user_id',
        'nip',
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
        'jabatan',
        'status',
        'tanggal_masuk',
    ];

    // ==========================================
    // 1. RELASI UTAMA (PARENT)
    // ==========================================

    // Relasi ke User (Akun Login)
    // Penting untuk ambil nama/email dari tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==========================================
    // 2. RELASI JABATAN KHUSUS (CHILDREN)
    // ==========================================

    // Relasi jika guru ini adalah Wali Kelas
    public function waliKelas()
    {
        return $this->hasOne(Classroom::class, 'wali_kelas_id');
    }

    // Relasi jika guru ini adalah Guru Mengaji
    public function guruMengaji()
    {
        return $this->hasOne(Classroom::class, 'guru_ngaji_id');
    }

    // Relasi jika guru ini adalah Guru Olahraga
    public function guruOlahraga()
    {
        return $this->hasOne(Classroom::class, 'guru_olahraga_id');
    }
}
