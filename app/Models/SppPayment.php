<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'amount',
        'due_date',
        'month',
        'year',
        'status',
        'proof_file',
        'note',
        'paid_at',
        'is_published',

        // --- TAMBAHAN FITUR DISPENSASI ---
        'dispensation_date',    // Tanggal janji
        'dispensation_note',    // Alasan
        'dispensation_status',  // pending, approved, rejected
    ];

    protected $casts = [
        'status' => 'string',
        'paid_at' => 'datetime',

        // Agar tanggal janji otomatis jadi object Carbon (bisa diformat)
        'dispensation_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Relasi ke Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Accessor untuk nama bulan (opsional)
     */
    public function getMonthNameAttribute()
    {
        return $this->month;
    }
}
