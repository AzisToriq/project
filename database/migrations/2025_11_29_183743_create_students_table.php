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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users (akun login)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('role')->default('murid');

            // Akun login orang tua
            $table->foreignId('parent_user_id')->nullable()->constrained('users')->onDelete('cascade');


            // Relasi ke tabel classrooms
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');

            // === Data Identitas Murid ===
            $table->string('nisn')->unique()->nullable();
            $table->string('nis')->unique()->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('nik')->unique()->nullable();

            // === Kontak & Alamat ===
            $table->string('nomor_telepon')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('dusun')->nullable();
            $table->string('kode_pos')->nullable();

            // === Akademik ===
            $table->integer('tahun_masuk')->nullable();
            $table->enum('status_aktif', ['Aktif', 'Lulus', 'Pindah', 'Cuti'])->default('Aktif');

            // === Orang Tua/Wali ===
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('nomor_telepon_ortu')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
