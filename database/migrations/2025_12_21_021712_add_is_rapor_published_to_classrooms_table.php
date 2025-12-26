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
    Schema::table('classrooms', function (Blueprint $table) {
        // Kolom untuk status rapor (Default: false/tertutup)
        $table->boolean('is_rapor_published')->default(false)->after('wali_kelas_id');
    });
}

public function down(): void
{
    Schema::table('classrooms', function (Blueprint $table) {
        $table->dropColumn('is_rapor_published');
    });
}
};
