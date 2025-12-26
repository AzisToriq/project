<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();

            // Identitas Kelas
            $table->string('nama_kelas'); // 1A, 1B
            $table->integer('level');     // 1, 2, 3 (Integer biar gampang disortir)
            $table->string('major')->default('Umum'); // Jurusan

            // INI YANG WAJIB ADA (Biar Seeder gak error)
            $table->string('academic_year'); // Contoh: "2025/2026"

            // Wali Kelas (Cuma 1 per kelas)
            $table->foreignId('wali_kelas_id')
                  ->nullable()
                  ->constrained('teachers')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
