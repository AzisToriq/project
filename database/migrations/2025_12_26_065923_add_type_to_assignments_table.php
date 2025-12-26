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
        // 1. Tambah kolom 'type' di tabel assignments
        Schema::table('assignments', function (Blueprint $table) {
            // Kita taruh setelah kolom 'deadline' biar rapi
            $table->string('type')->default('offline')->after('deadline');
        });

        // 2. Tambah kolom 'feedback' di tabel assignment_submissions
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Kita taruh setelah kolom 'grade'
            $table->text('feedback')->nullable()->after('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom jika migrasi di-rollback
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropColumn('feedback');
        });
    }
};
