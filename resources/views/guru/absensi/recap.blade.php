@extends('layouts.app')

@section('content')
<div class="py-4">
    {{-- NAVIGASI & HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('guru.absensi.mapel', $allocation->classroom_id) }}" class="btn btn-link text-secondary pl-0 text-decoration-none fw-bold">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke List Mapel
        </a>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-print me-2"></i> Cetak Laporan
        </button>
    </div>

    {{-- KOP LAPORAN (Hanya muncul saat Print) --}}
    <div class="d-none d-print-block text-center mb-4">
        <h3 class="fw-bold mb-0">LAPORAN REKAPITULASI KEHADIRAN</h3>
        <p class="mb-0 text-muted">{{ $allocation->subject->nama_mapel }} - Kelas {{ $allocation->classroom->nama_kelas }}</p>
        <hr>
    </div>

    {{-- KARTU STATISTIK UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="text-uppercase text-muted small fw-bold mb-1">Mata Pelajaran</h5>
                    <h2 class="fw-bold text-dark mb-0">{{ $allocation->subject->nama_mapel }}</h2>
                    <div class="mt-2">
                        <span class="badge bg-primary me-2">Kelas {{ $allocation->classroom->nama_kelas }}</span>
                        <span class="text-muted small"><i class="fas fa-code me-1"></i> {{ $allocation->subject->code }}</span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0 no-print">
                    <div class="d-inline-block bg-light rounded-3 p-3 text-start" style="min-width: 180px;">
                        <small class="text-muted d-block fw-bold text-uppercase">Total Pertemuan</small>
                        <span class="h1 fw-bold text-primary mb-0">{{ $totalPertemuan }}</span> <span class="small text-muted">Sesi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 1: STATISTIK PER SISWA (REPORT CARD) --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="fw-bold m-0 text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>Statistik Kehadiran Siswa</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-striped-columns">
                    <thead class="bg-primary text-white text-uppercase small">
                        <tr>
                            <th class="px-4 py-3" width="50">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center" width="80">H</th>
                            <th class="text-center" width="80">S</th>
                            <th class="text-center" width="80">I</th>
                            <th class="text-center" width="80">A</th>
                            <th class="text-center" width="150">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recapData as $index => $data)
                        <tr>
                            <td class="px-4 text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $data['student']->nama_lengkap }}</div>
                                <small class="text-muted no-print" style="font-size: 11px;">{{ $data['student']->nis ?? '-' }}</small>
                            </td>

                            {{-- ANGKA STATISTIK --}}
                            <td class="text-center fw-bold text-success bg-success bg-opacity-10">{{ $data['H'] }}</td>
                            <td class="text-center text-primary">{{ $data['S'] }}</td>
                            <td class="text-center text-warning">{{ $data['I'] }}</td>
                            <td class="text-center text-danger">{{ $data['A'] }}</td>

                            {{-- PROGRESS BAR --}}
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 me-2 no-print">
                                        <div class="progress" style="height: 6px; background-color: #e9ecef;">
                                            <div class="progress-bar {{ $data['percent'] < 75 ? 'bg-danger' : 'bg-success' }}"
                                                 role="progressbar"
                                                 style="width: {{ $data['percent'] }}%; border-radius: 10px;">
                                            </div>
                                        </div>
                                    </div>
                                    <span class="fw-bold small {{ $data['percent'] < 75 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($data['percent'], 0) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data absensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: RIWAYAT PERTEMUAN (HISTORY LOG) --}}
    <div class="row no-print">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-history me-2"></i>Riwayat Pertemuan</h5>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted text-uppercase small">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Total Hadir</th>
                                    <th class="text-end px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyDates as $hist)
                                <tr>
                                    <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($hist->date)->format('d F Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            {{ \Carbon\Carbon::parse($hist->date)->locale('id')->translatedFormat('l') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                            <i class="fas fa-user-check me-1"></i> {{ $hist->total_hadir }} Hadir
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        {{-- LOGIC ETIKA: Tombol ini melempar ke Form Input --}}
                                        {{-- Pesannya "Buka/Detail", bukan "Edit" kasar --}}
                                        <a href="{{ route('guru.absensi.input', ['allocation_id' => $allocation->id, 'date' => $hist->date]) }}"
                                           class="btn btn-sm btn-outline-info rounded-pill px-3 me-1"
                                           data-bs-toggle="tooltip" title="Buka Detail / Revisi">
                                            <i class="fas fa-external-link-alt me-1"></i> Buka
                                        </a>

                                        {{-- Tombol Delete --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                onclick="confirmDelete('{{ \Carbon\Carbon::parse($hist->date)->format('d F Y') }}', '{{ route('guru.absensi.destroy', ['allocation_id' => $allocation->id, 'date' => $hist->date]) }}')"
                                                data-bs-toggle="tooltip" title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat pertemuan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Delete Hidden --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

</div>

{{-- Script SweetAlert & Tooltip --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(dateFormatted, url) {
        Swal.fire({
            title: 'Hapus Riwayat?',
            text: "Data absensi tanggal " + dateFormatted + " akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
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

    // Aktifkan Tooltip Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .progress-bar { -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
