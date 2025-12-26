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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users (akun login)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            // Identitas Guru
            $table->string('nip')->unique()->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('nik')->unique()->nullable();

            // Kontak & Alamat
            $table->string('nomor_telepon')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('dusun')->nullable();
            $table->string('kode_pos')->nullable();

            // Status Pegawai
            $table->enum('jabatan', [
                'Wali Kelas',
                'Guru Olahraga',
                'Guru Mengaji',
            ])->nullable();

            $table->enum('status', ['Aktif', 'Cuti', 'Pindah', 'Pensiun'])->default('Aktif');

            // Tanggal masuk
            $table->date('tanggal_masuk')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
