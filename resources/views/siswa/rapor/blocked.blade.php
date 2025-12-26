@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-6 col-lg-5">

            {{-- Kartu Blokir --}}
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 text-center">

                {{-- Ikon Gembok Besar --}}
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle" style="width: 120px; height: 120px;">
                        <i class="fas fa-lock fa-5x text-warning"></i>
                    </div>
                </div>

                {{-- Judul (Dinamis dari Controller) --}}
                <h2 class="fw-bold text-dark mb-3">
                    {{ $title ?? 'Akses Terkunci' }}
                </h2>

                {{-- Pesan Penjelasan (Dinamis dari Controller) --}}
                <p class="text-muted lead mb-4" style="font-size: 1.1rem;">
                    {{ $message ?? 'Maaf, halaman ini belum dapat diakses saat ini. Silakan kembali lagi nanti.' }}
                </p>

                {{-- Garis Pemisah Kecil --}}
                <hr class="w-50 mx-auto mb-4 opacity-25">

                {{-- Tombol Kembali --}}
                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm hover-scale">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                </a>

            </div>

            {{-- Footer Kecil --}}
            <div class="text-center mt-4 text-muted small">
                &copy; {{ date('Y') }} Sistem Informasi Sekolah
            </div>

        </div>
    </div>
</div>

{{-- CSS Tambahan untuk Animasi Tombol --}}
<style>
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: scale(1.05);
    }
</style>
@endsection
