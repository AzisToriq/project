@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">📅 Jadwal Mengajar Saya</h3>
            <p class="text-muted small">Kelola kelas dan absensi sesuai jadwal yang telah ditentukan.</p>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-primary" id="current-time"></h5>
            <p class="text-muted small mb-0">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    {{-- STATS SINGKAT --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                <div class="card-body">
                    <div class="small opacity-75">Total Jam Mengajar</div>
                    <div class="h2 fw-bold mb-0">{{ $schedules->count() }} <small class="h6">Sesi</small></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL JADWAL --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3">Waktu & Hari</th>
                            <th>Ruang / Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th class="text-center">Status</th>
                            <th class="text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($schedules as $s)
                        @php
                            $isToday = $s->day == \Carbon\Carbon::now()->locale('id')->translatedFormat('l');
                        @endphp
                        <tr class="{{ $isToday ? 'bg-primary bg-opacity-10' : '' }}">
                            <td class="px-4">
                                <div class="fw-bold text-dark">{{ $s->day }}</div>
                                <div class="small">
                                    {{-- BAGIAN JAM --}}
                                    <span class="badge bg-white text-primary border border-primary border-opacity-25 rounded-pill px-2">
                                        <i class="far fa-clock me-1"></i>
                                        {{-- Gunakan Carbon parse agar format jam konsisten --}}
                                        {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-school small"></i>
                                    </div>
                                    {{-- BAGIAN KELAS --}}
                                    <span class="fw-bold text-dark">{{ $s->teacher_allocation->classroom->nama_kelas ?? 'Kelas Tdk Ditemukan' }}</span>
                                </div>
                            </td>
                            <td>
                                {{-- BAGIAN MAPEL --}}
                                <div class="fw-bold text-dark">{{ $s->teacher_allocation->subject->nama_mapel ?? 'Mapel Tdk Ditemukan' }}</div>
                                <div class="text-muted small">Kode: {{ $s->teacher_allocation->subject->code ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                @if($isToday)
                                    <span class="badge rounded-pill bg-success shadow-sm px-3">
                                        <i class="fas fa-circle fa-xs me-1 animate-pulse"></i> Hari Ini
                                    </span>
                                @else
                                    <span class="text-muted small italic">Mendatang</span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('guru.absensi.input', $s->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="fas fa-user-check me-1"></i> Absen
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-calendar-alt fa-3x text-light mb-3"></i>
                                    <h6 class="text-muted fw-normal">Anda belum memiliki jadwal mengajar terdaftar.</h6>
                                    <small class="text-muted">Silakan hubungi bagian kurikulum/admin.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animasi denyut untuk badge Hari Ini */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    .btn-white {
        background: white;
        color: #4e73df;
    }
    .btn-white:hover {
        background: #f8f9fc;
    }
</style>

<script>
    // Script jam digital real-time
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const element = document.getElementById('current-time');
        if(element) element.textContent = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
