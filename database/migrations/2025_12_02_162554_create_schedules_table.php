<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // KUNCINYA DISINI:
            // Kita relasikan ke tabel 'teacher_allocations' (Plotting).
            // Di tabel plotting itu udah ada data: Guru Siapa, Mapel Apa, Kelas Mana, Tahun Berapa.
            // Jadi kita gak perlu tulis ulang di sini biar hemat & konsisten.
            $table->foreignId('teacher_allocation_id')
                  ->constrained('teacher_allocations')
                  ->onDelete('cascade');

            // Data Waktu
            $table->string('day'); // Senin, Selasa, dll
            $table->time('start_time');
            $table->time('end_time');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
