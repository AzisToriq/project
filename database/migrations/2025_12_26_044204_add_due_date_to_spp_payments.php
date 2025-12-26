<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('spp_payments', function (Blueprint $table) {
        $table->date('due_date')->nullable()->after('amount'); // Tanggal Jatuh Tempo
    });
}

public function down()
{
    Schema::table('spp_payments', function (Blueprint $table) {
        $table->dropColumn('due_date');
    });
}
};
