<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // RELASI
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_allocation_id')->constrained('teacher_allocations')->onDelete('cascade');

            // DATA NILAI
            $table->string('type'); // Contoh: 'UH1', 'UH2', 'UTS', 'UAS', 'TUGAS'
            $table->integer('score')->default(0); // Nilainya (0-100)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
