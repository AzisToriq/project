<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // 1. KUNCI UTAMA (Anti Bug Jadwal Ganda)
            // Kolom ini wajib ada biar sistem tau ini absen jam berapa.
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');

            // 2. DATA SISWA & MAPEL
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_allocation_id')->constrained('teacher_allocations')->onDelete('cascade');

            // 3. DATA ABSENSI
            $table->date('date'); // Tanggal absen dilakukan
            $table->string('status', 5)->default('H'); // H, S, I, A
            $table->text('note')->nullable(); // Catatan per siswa (misal: "Sakit Demam")

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
