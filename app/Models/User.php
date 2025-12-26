<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username', // Penting buat login NIP/NIS/Username Ortu
        'email',
        'password',
        'role',     // admin, teacher, student, parent
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean', // Biar jadi true/false di kodingan
        ];
    }

    // ==========================================
    // RELASI KE PROFIL (USER DATA)
    // ==========================================

    /**
     * Relasi ke Guru (1 User = 1 Guru)
     * Digunakan jika role = 'teacher'
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    /**
     * Relasi ke Siswa (1 User = 1 Siswa)
     * Digunakan jika role = 'student'
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Relasi ke Data Anak (1 User Ortu = Banyak Anak/Siswa)
     * Digunakan jika role = 'parent'
     * PENTING: Nama fungsi harus 'students' agar cocok dengan controller
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_user_id');
    }

    // ==========================================
    // HELPER FUNCTION
    // ==========================================

    /**
     * Cek Role User
     * Contoh penggunaan: if ($user->hasRole('admin')) { ... }
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }
}
