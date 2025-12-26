<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi balik ke Tugas
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Relasi ke Siswa yang ngumpulin
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
