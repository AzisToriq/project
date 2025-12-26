@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- Header Judul --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Laporan Hasil Belajar</h2>
        <p class="text-muted">Evaluasi Pencapaian Kompetensi Peserta Didik</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            {{-- Kartu Sampul Rapor --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden transform-hover">

                {{-- Bagian Atas: Header Biru --}}
                <div class="bg-primary p-4 text-white text-center position-relative">
                    {{-- Hiasan Pattern (Opsional) --}}
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-10"
                         style="background-image: radial-gradient(circle, #ffffff 2px, transparent 2.5px); background-size: 10px 10px;">
                    </div>

                    <h5 class="mb-1 position-relative fw-bold">SEMESTER GANJIL</h5>
                    <p class="mb-0 opacity-75 position-relative">Tahun Ajaran 2025/2026</p>
                </div>

                {{-- Bagian Tengah: Profil Siswa --}}
                <div class="card-body p-4 p-md-5 text-center bg-white">

                    {{-- Avatar Ikon Toga (Pengganti Inisial C) --}}
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary border border-3 border-light rounded-circle shadow-sm"
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-user-graduate fa-3x"></i>
                        </div>
                    </div>

                    {{-- Nama & Kelas --}}
                    <h4 class="fw-bold text-dark mb-1">{{ $student->nama_lengkap }}</h4>
                    <p class="text-muted mb-3">
                        NISN: <span class="font-monospace text-dark">{{ $student->nisn }}</span>
                    </p>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-4">
                        Kelas {{ $student->classroom->nama_kelas }}
                    </span>

                    {{-- Garis Pemisah --}}
                    <hr class="my-4 opacity-10">

                    {{-- Status Rapor --}}
                    <div class="alert alert-success d-flex align-items-center border-0 bg-success bg-opacity-10 text-success rounded-3 mb-4">
                        <i class="fas fa-check-circle me-3 fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold">Rapor Tersedia</div>
                            <small>Sudah dipublikasikan oleh Wali Kelas.</small>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI UTAMA --}}
                    <div class="d-grid gap-2">
                        {{-- Link ke Route 'print' yang menampilkan kertas A4 --}}
                        <a href="{{ route('siswa.rapor.print') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold">
                            <i class="fas fa-book-open me-2"></i> Buka Rapor Lengkap
                        </a>

                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg rounded-pill text-muted">
                            Kembali ke Dashboard
                        </a>
                    </div>

                </div>

                {{-- Footer Card --}}
                <div class="card-footer bg-light text-center py-3 border-0">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        <i class="fas fa-school me-1"></i>
                        SD Baitul Insan
                    </small>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- CSS Tambahan untuk Efek Hover --}}
<style>
    .transform-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .transform-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
</style>
@endsection
