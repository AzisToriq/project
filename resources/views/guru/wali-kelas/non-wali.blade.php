@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5">

            {{-- Card Utama --}}
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 text-center position-relative overflow-hidden">

                {{-- Hiasan Background (Opsional - Biar ada aksen) --}}
                <div class="position-absolute top-0 start-50 translate-middle rounded-circle bg-primary opacity-10" style="width: 200px; height: 200px; filter: blur(40px);"></div>

                <div class="position-relative z-1">
                    {{-- Ikon --}}
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 100px; height: 100px;">
                            <i class="fas fa-chalkboard-teacher fa-3x"></i>
                        </div>
                    </div>

                    {{-- Judul & Deskripsi --}}
                    <h3 class="fw-bold text-dark mb-3">Akses Wali Kelas</h3>
                    <p class="text-muted mb-4 lead" style="font-size: 1rem; line-height: 1.6;">
                        Halo, <strong>{{ Auth::user()->name }}</strong>.<br>
                        Saat ini akun Anda belum terdaftar sebagai <span class="fw-bold text-primary">Wali Kelas</span> aktif di sistem.
                    </p>

                    <div class="alert alert-light border-0 bg-light rounded-3 text-start small mb-4">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <span class="text-muted">Menu ini khusus untuk memonitoring nilai, absensi, dan rapor siswa perwalian.</span>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                        </a>

                        {{-- Opsi Hubungi Admin (Jika ini kesalahan sistem) --}}
                        <a href="#" class="btn btn-link text-decoration-none text-muted btn-sm mt-2">
                            Merasa ini kesalahan? Hubungi Admin
                        </a>
                    </div>
                </div>
            </div>

            {{-- Footer Kecil --}}
            <div class="text-center mt-4 text-muted opacity-50 small">
                &copy; {{ date('Y') }} Sistem Informasi Akademik
            </div>

        </div>
    </div>
</div>
@endsection
