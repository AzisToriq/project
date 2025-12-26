<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('spp_payments', function (Blueprint $table) {
        $table->date('dispensation_date')->nullable()->after('status'); // Tanggal Janji Bayar
        $table->string('dispensation_note')->nullable()->after('dispensation_date'); // Alasan (Menunggu Gajian, dll)
    });
}

public function down()
{
    Schema::table('spp_payments', function (Blueprint $table) {
        $table->dropColumn(['dispensation_date', 'dispensation_note']);
    });
}
};
