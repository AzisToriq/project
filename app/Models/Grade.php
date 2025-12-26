<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_allocation_id', // Link ke Mapel/Guru tertentu
        'type',  // UH1, UTS, UAS, dll
        'score'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher_allocation()
    {
        return $this->belongsTo(TeacherAllocation::class);
    }
}
