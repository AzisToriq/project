@extends('layouts.app')

@section('content')
<div class="py-4">
    {{-- HEADER: NAVIGASI BALIK & JUDUL --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('guru.absensi.index') }}" class="btn btn-light rounded-circle shadow-sm me-3 text-secondary" data-bs-toggle="tooltip" title="Kembali ke Folder Kelas">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold mb-0">Mata Pelajaran</h1>
            <p class="text-muted small mb-0">Daftar mapel ajar Anda di <span class="badge bg-primary px-3 rounded-pill">Kelas {{ $classroom->nama_kelas }}</span></p>
        </div>
    </div>

    {{-- GRID MAPEL --}}
    <div class="row">
        @forelse($allocations as $alloc)
        <div class="col-xl-4 col-md-6 mb-4">

            {{-- CARD MAPEL --}}
            <div class="card border-0 shadow-sm h-100 py-2 hover-mapel position-relative overflow-hidden" style="border-radius: 16px;">

                <div class="card-body">
                    {{-- INFO MAPEL --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="font-weight-bold text-dark mb-1">{{ $alloc->subject->nama_mapel }}</h5>
                            <span class="badge bg-light text-secondary border rounded-pill">
                                <i class="fas fa-code me-1"></i> {{ $alloc->subject->code }}
                            </span>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="fas fa-book-open fa-lg"></i>
                        </div>
                    </div>

                    <hr class="my-3 border-light">

                    {{-- TOMBOL AKSI --}}
                    <div class="d-grid gap-2">
                        {{-- 1. Tombol Input (Primary Action) --}}
                        <a href="{{ route('guru.absensi.input', $alloc->id) }}" class="btn btn-outline-success fw-bold rounded-pill py-2 shadow-sm action-btn">
                            <i class="fas fa-pen me-2"></i> Input Absensi
                        </a>

                        <div class="row g-2">
                            {{-- 2. Tombol Jurnal (Secondary) --}}
                            <div class="col-6">
                                <a href="{{ route('guru.absensi.jurnal', $alloc->id) }}" class="btn btn-sm btn-light w-100 text-secondary border rounded-pill py-2" title="Lihat Riwayat">
                                    <i class="fas fa-history me-1"></i> Jurnal
                                </a>
                            </div>

                            {{-- 3. Tombol Rekap (Secondary) --}}
                            <div class="col-6">
                                <a href="{{ route('guru.absensi.rekap', $alloc->id) }}" class="btn btn-sm btn-light w-100 text-secondary border rounded-pill py-2" title="Laporan Statistik">
                                    <i class="fas fa-chart-pie me-1"></i> Rekap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @empty
        {{-- STATE KOSONG --}}
        <div class="col-12 text-center py-5">
            <div class="d-inline-block p-4 rounded-circle bg-light mb-3">
                <i class="fas fa-box-open fa-3x text-muted opacity-50"></i>
            </div>
            <h5 class="text-muted fw-bold">Tidak ada mata pelajaran</h5>
            <p class="text-muted small">Anda belum di-plotting untuk mengajar mapel apapun di kelas ini.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
    /* Efek Hover Hijau Khas Mapel */
    .hover-mapel {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid transparent;
        background: #fff;
    }

    .hover-mapel:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(28, 200, 138, 0.15) !important;
        border-color: #1cc88a !important; /* Hijau Success */
    }

    /* Animasi Icon Buku */
    .hover-mapel:hover .icon-box {
        background-color: #1cc88a !important;
        color: white !important;
        transform: scale(1.1) rotate(5deg);
        transition: all 0.3s;
    }

    /* FITUR REQUEST LU:
       Saat cursor mengarah ke CARD (.hover-mapel:hover),
       tombol Input (.action-btn) otomatis berubah jadi HIJAU SOLID.
    */
    .hover-mapel:hover .action-btn {
        background-color: #1cc88a;
        color: white;
        border-color: #1cc88a;
        transition: all 0.3s;
    }

    /* Tombol Jurnal & Rekap jadi sedikit lebih gelap saat hover card */
    .hover-mapel:hover .btn-light {
        background-color: #f8f9fa;
        border-color: #e3e6f0;
    }
</style>
@endsection
