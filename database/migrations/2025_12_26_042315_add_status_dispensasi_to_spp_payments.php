<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            // Kita tambahkan kolom status persetujuan
            // Default NULL artinya belum ada pengajuan
            $table->enum('dispensation_status', ['pending', 'approved', 'rejected'])
                  ->nullable()
                  ->after('dispensation_note');
        });
    }

    public function down()
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            $table->dropColumn('dispensation_status');
        });
    }
};
