<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            // 1. Lepas Foreign Key dulu (biar indexnya bebas)
            // Menggunakan array ['student_id'] agar Laravel otomatis mencari nama constraint-nya
            $table->dropForeign(['student_id']);

            // 2. Hapus Index Unik (Penyebab Error Tadi)
            $table->dropUnique('spp_payments_student_id_month_year_unique');

            // 3. Pasang lagi Foreign Key-nya (Akan otomatis buat index biasa/non-unique)
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            // Urutan kebalikannya jika rollback
            $table->dropForeign(['student_id']);

            // Kembalikan Unique Index
            $table->unique(['student_id', 'month', 'year'], 'spp_payments_student_id_month_year_unique');

            // Pasang FK lagi
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }
};
