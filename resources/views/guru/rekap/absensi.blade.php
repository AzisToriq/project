@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('guru.absensi.mapel', $allocation->classroom_id) }}" class="btn btn-link text-secondary p-0 mb-2 text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Mapel
            </a>
            <h1 class="h3 text-gray-800 font-weight-bold">📅 Jurnal Absensi</h1>
            <p class="text-muted small mb-0">
                Mapel: <b>{{ $allocation->subject->name }}</b> | Kelas: <b>{{ $allocation->classroom->name }}</b>
            </p>
        </div>

        <a href="{{ route('guru.absensi.manual', ['allocation_id' => $allocation->id, 'date' => date('Y-m-d')]) }}" class="btn btn-primary shadow-sm rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i> Absen Hari Ini
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white h-100 py-2 rounded-lg">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-white-50">Total Pertemuan</div>
                        <div class="h3 fw-bold mb-0">{{ $history->count() }}</div>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100 py-2 rounded-lg">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3">Grafik Kehadiran (Sample)</h6>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" style="width: 80%">Hadir</div>
                        <div class="progress-bar bg-primary" style="width: 5%">S</div>
                        <div class="progress-bar bg-warning text-dark" style="width: 5%">I</div>
                        <div class="progress-bar bg-danger" style="width: 10%">A</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-header bg-white border-bottom-0 p-0">
            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                        <i class="fas fa-history me-2"></i> Riwayat Harian (Edit/Hapus)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 fw-bold" id="rekap-tab" data-bs-toggle="tab" data-bs-target="#rekap" type="button">
                        <i class="fas fa-chart-bar me-2"></i> Rekapitulasi Siswa
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="myTabContent">

                <div class="tab-pane fade show active" id="history" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Tanggal Absensi</th>
                                    <th>Status Input</th>
                                    <th class="text-end px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $h)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($h->date)->locale('id')->translatedFormat('l, d F Y') }}
                                        </div>

                                        <small class="text-muted">
                                            @if($h->updated_at > $h->created_at)
                                                <i class="fas fa-edit text-warning me-1"></i>
                                                Diperbarui: {{ $h->updated_at->locale('id')->diffForHumans() }}
                                            @else
                                                <i class="fas fa-clock text-secondary me-1"></i>
                                                Diinput: {{ $h->created_at->locale('id')->diffForHumans() }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Data Tersimpan
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <a href="{{ route('guru.absensi.manual', ['allocation_id' => $allocation->id, 'date' => $h->date]) }}"
                                           class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2">
                                            <i class="fas fa-pencil-alt me-1"></i> Edit
                                        </a>

                                        <button onclick="hapusAbsensi('{{ $h->date }}')" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>

                                        <form id="form-hapus-{{ $h->date }}" action="{{ route('guru.absensi.hapus_per_tanggal', $allocation->id) }}" method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="date" value="{{ $h->date }}">
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                                        <p class="text-muted">Belum ada riwayat absensi.</p>
                                        <a href="{{ route('guru.absensi.manual', ['allocation_id' => $allocation->id]) }}" class="btn btn-primary btn-sm mt-2">
                                            Mulai Absen
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="rekap" role="tabpanel">
                    <div class="p-3 text-end bg-light border-bottom">
                        <button class="btn btn-sm btn-success shadow-sm" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Cetak Laporan
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th rowspan="2" style="vertical-align: middle;">No</th>
                                    <th rowspan="2" style="vertical-align: middle; text-align: left;" class="px-4">Nama Siswa</th>
                                    <th colspan="4">Total</th>
                                    <th rowspan="2" style="vertical-align: middle;">% Hadir</th>
                                </tr>
                                <tr>
                                    <th class="text-success">H</th>
                                    <th class="text-primary">S</th>
                                    <th class="text-warning">I</th>
                                    <th class="text-danger">A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $s)
                                <tr class="text-center">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start px-4 fw-bold">{{ $s->name }}</td>
                                    <td class="bg-success bg-opacity-10 fw-bold text-success">{{ $s->hadir_count }}</td>
                                    <td>{{ $s->sakit_count }}</td>
                                    <td>{{ $s->izin_count }}</td>
                                    <td>{{ $s->alpha_count }}</td>
                                    <td>
                                        <span class="badge {{ $s->persentase >= 70 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $s->persentase }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function hapusAbsensi(date) {
        Swal.fire({
            title: 'Hapus Absensi?',
            text: "Data absensi pada tanggal " + date + " akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-' + date).submit();
            }
        });
    }
</script>
@endpush

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        transition: 0.3s;
    }
    .nav-tabs .nav-link.active {
        color: #4e73df;
        border-bottom: 3px solid #4e73df;
        background: transparent;
    }
    .nav-tabs .nav-link:hover {
        color: #4e73df;
        background-color: #f8f9fc;
    }
</style>
@endsection
