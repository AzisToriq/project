<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_allocations', function (Blueprint $table) {
            $table->id();

            // Relasi Guru
            $table->foreignId('teacher_id')
                  ->constrained('teachers')
                  ->onDelete('cascade');

            // Relasi Kelas & Mapel
            $table->foreignId('classroom_id')
                  ->constrained('classrooms')
                  ->onDelete('cascade');

            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade');

            // Tambahan Info Akademik
            $table->string('semester')->default('Ganjil');
            $table->string('academic_year'); // Contoh: 2025/2026

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_allocations');
    }
};
