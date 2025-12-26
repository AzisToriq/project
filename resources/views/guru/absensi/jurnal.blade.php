@extends('layouts.app')

@section('content')
<div class="py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('guru.absensi.mapel', $allocation->classroom_id) }}" class="btn btn-light rounded-circle shadow-sm me-3 text-secondary">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 text-gray-800 font-weight-bold mb-0">Jurnal Pertemuan</h1>
                <p class="text-muted small mb-0">
                    Riwayat aktivitas mengajar di <span class="badge bg-primary">Kelas {{ $allocation->classroom->nama_kelas }}</span>
                </p>
            </div>
        </div>

        <a href="{{ route('guru.absensi.input', $allocation->id) }}" class="btn btn-success rounded-pill shadow-sm px-4 fw-bold">
            <i class="fas fa-plus me-2"></i> Absensi Baru
        </a>
    </div>

    <div class="row">
        @forelse($riwayat as $r)
        @php
            // Logic Urutan: Menggunakan loop iteration karena data sudah di-sort ASC di Controller
            $pertemuanKe = $loop->iteration;
            $tanggalIndo = \Carbon\Carbon::parse($r->date)->locale('id')->isoFormat('dddd, D MMMM Y');
        @endphp

        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px;">

                <div class="card-body">
                    {{-- HEADER CARD: TANGGAL & BADGE (FLEXBOX BIAR GAK NABRAK) --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        {{-- Bagian Kiri: Tanggal --}}
                        <div>
                            <h5 class="fw-bold text-dark mb-1 text-capitalize lh-sm">
                                {{ $tanggalIndo }}
                            </h5>
                            <span class="text-muted small">
                                <i class="far fa-clock me-1"></i> Tercatat di sistem
                            </span>
                        </div>

                        {{-- Bagian Kanan: Badge Pertemuan --}}
                        <div class="ms-2">
                            <span class="badge bg-primary bg-gradient rounded-pill px-3 py-2 shadow-sm text-nowrap">
                                Ke-{{ $pertemuanKe }}
                            </span>
                        </div>
                    </div>

                    {{-- RINGKASAN ANGKA --}}
                    <div class="d-flex justify-content-between mb-4 text-center bg-light rounded-4 p-3 border border-light">
                        <div class="px-1">
                            <small class="d-block text-success fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Hadir</small>
                            <span class="h4 mb-0 fw-bold text-dark">{{ $r->hadir }}</span>
                        </div>
                        <div class="px-1 border-start border-2">
                            <small class="d-block text-primary fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Sakit</small>
                            <span class="h4 mb-0 fw-bold text-dark">{{ $r->sakit }}</span>
                        </div>
                        <div class="px-1 border-start border-2">
                            <small class="d-block text-warning fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Izin</small>
                            <span class="h4 mb-0 fw-bold text-dark">{{ $r->izin }}</span>
                        </div>
                        <div class="px-1 border-start border-2">
                            <small class="d-block text-danger fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Alpha</small>
                            <span class="h4 mb-0 fw-bold text-dark">{{ $r->alpha }}</span>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('guru.absensi.input', ['allocation_id' => $allocation->id, 'date' => $r->date]) }}"
                           class="btn btn-outline-primary rounded-pill fw-bold py-2 shadow-sm">
                            <i class="fas fa-edit me-2"></i> Edit Absensi
                        </a>

                        <button type="button" class="btn btn-sm text-danger text-decoration-none mt-1"
                                onclick="confirmDelete('{{ $tanggalIndo }}', '{{ route('guru.absensi.destroy', ['allocation_id' => $allocation->id, 'date' => $r->date]) }}')">
                           <i class="fas fa-trash me-1"></i> Hapus Pertemuan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="d-inline-block p-4 rounded-circle bg-light mb-3">
                <i class="fas fa-history fa-3x text-muted opacity-50"></i>
            </div>
            <h5 class="text-muted fw-bold">Jurnal Kosong</h5>
            <p class="text-muted small">Belum ada kegiatan belajar mengajar yang tercatat.</p>
        </div>
        @endforelse
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
</div>

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(dateFormatted, url) {
        Swal.fire({
            title: 'Hapus Pertemuan?',
            text: "Data " + dateFormatted + " akan dihapus. Yakin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        })
    }
</script>

<style>
    .hover-card { transition: all 0.3s ease; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .text-capitalize { text-transform: capitalize !important; }
    /* Perbaikan Layout Angka */
    .h4 { font-size: 1.25rem; }
</style>
@endsection
