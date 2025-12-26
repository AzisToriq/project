<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spp_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // Kolom Nominal
            $table->bigInteger('amount'); // Nominal Tagihan (Contoh: 150000)

            // Waktu Tagihan
            $table->string('month'); // Januari, Februari, dst
            $table->integer('year'); // 2025

            // Status Pembayaran: Unpaid (Belum), Pending (Diperiksa), Paid (Lunas)
            $table->enum('status', ['unpaid', 'pending', 'paid'])->default('unpaid');

            // FITUR BARU: Status Publikasi (Draft / Terbit)
            // Default false (0) artinya DRAFT (Disembunyikan dari Ortu)
            $table->boolean('is_published')->default(false);

            // Bukti Transfer (Path Gambar)
            $table->string('proof_file')->nullable();

            // Catatan (Opsional)
            $table->string('note')->nullable();

            // Tanggal dikonfirmasi Lunas
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();

            // Mencegah duplikat tagihan bulan yang sama untuk siswa yang sama
            $table->unique(['student_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spp_payments');
    }
};
