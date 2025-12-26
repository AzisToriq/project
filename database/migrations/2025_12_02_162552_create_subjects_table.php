<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            // DATA MAPEL
            // Ganti 'kode_mapel' jadi 'code' biar cocok sama Seeder
            // Kasih unique() biar gak ada mapel dobel kodenya
            $table->string('code')->unique();

            $table->string('nama_mapel');

            // teacher_id DIHAPUS AJA.
            // Kenapa? Karena kita udah punya tabel 'teacher_allocations' (Plotting).
            // Jadi guru pengampu itu datanya ada di tabel plotting, bukan nempel di mapel.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
