<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perkembangan Siswa
        </h2>
    </x-slot>

    <div class="container-fluid px-4 mt-4">

        {{-- HEADER PROFILE --}}
        <div class="card border-0 rounded-4 shadow-sm mb-4 bg-primary text-white overflow-hidden position-relative">
            <div class="position-absolute end-0 bottom-0 opacity-10 p-3">
                <i class="fas fa-chart-line fa-8x"></i>
            </div>
            <div class="card-body p-4 position-relative z-1">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($anak->nama_lengkap) }}&background=ffffff&color=0d6efd&bold=true"
                         class="rounded-circle border border-3 border-white shadow-sm me-4" width="80">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $anak->nama_lengkap }}</h3>
                        <p class="mb-0 opacity-75">NISN: {{ $anak->nisn }} • Kelas {{ $anak->classroom->nama_kelas ?? '-' }}</p>
                        <div class="mt-2 badge bg-white text-primary rounded-pill px-3">
                            <i class="fas fa-check-circle me-1"></i> Status: Aktif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- KOLOM KIRI: STATISTIK ABSENSI --}}
            <div class="col-lg-8">
                {{-- 1. Cards Ringkasan --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm bg-success bg-opacity-10 h-100">
                            <div class="card-body text-center p-3">
                                <h2 class="fw-bold text-success mb-0">{{ $hadir }}</h2>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Hadir (H)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm bg-info bg-opacity-10 h-100">
                            <div class="card-body text-center p-3">
                                <h2 class="fw-bold text-info mb-0">{{ $izin }}</h2>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Izin (I)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm bg-warning bg-opacity-10 h-100">
                            <div class="card-body text-center p-3">
                                <h2 class="fw-bold text-warning mb-0">{{ $sakit }}</h2>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Sakit (S)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm bg-danger bg-opacity-10 h-100">
                            <div class="card-body text-center p-3">
                                <h2 class="fw-bold text-danger mb-0">{{ $alpha }}</h2>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Alpha (A)</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Grafik Progress Bar --}}
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Persentase Kehadiran</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold text-dark">Total Kehadiran</span>
                            <span class="fw-bold text-primary">{{ $persentase_kehadiran }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 25px;">
                            <div class="progress-bar bg-gradient-primary progress-bar-striped progress-bar-animated"
                                 role="progressbar"
                                 style="width: {{ $persentase_kehadiran }}%">
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            * Persentase dihitung dari total data absensi yang masuk.
                        </p>
                    </div>
                </div>

                {{-- 3. Riwayat Absensi Terakhir --}}
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold text-dark"><i class="fas fa-history text-secondary me-2"></i>Riwayat 5 Absensi Terakhir</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Tanggal</th>
                                        <th>Mapel</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat_absensi as $absen)
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark">{{ \Carbon\Carbon::parse($absen->date)->format('d M Y') }}</td>
                                            <td>
                                                @if($absen->teacher_allocation)
                                                    <span class="fw-bold text-primary">{{ $absen->teacher_allocation->subject->nama_mapel ?? 'Mapel Umum' }}</span>
                                                    <div class="small text-muted">{{ $absen->teacher_allocation->teacher->nama_lengkap ?? '-' }}</div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- LOGIC BADGE STATUS H, S, I, A --}}
                                                @if($absen->status == 'H') <span class="badge bg-success rounded-pill px-3">Hadir</span>
                                                @elseif($absen->status == 'I') <span class="badge bg-info rounded-pill px-3">Izin</span>
                                                @elseif($absen->status == 'S') <span class="badge bg-warning rounded-pill px-3">Sakit</span>
                                                @elseif($absen->status == 'A') <span class="badge bg-danger rounded-pill px-3">Alpha</span>
                                                @else <span class="badge bg-secondary rounded-pill px-3">{{ $absen->status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">{{ $absen->note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data absensi.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: NILAI TERBARU --}}
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold text-dark"><i class="fas fa-star text-warning me-2"></i>Nilai Terbaru</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($nilai_terbaru as $nilai)
                                <li class="list-group-item px-4 py-3 border-0 border-bottom d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $nilai->teacher_allocation->subject->nama_mapel ?? 'Mapel' }}</h6>
                                        <small class="text-muted">{{ $nilai->type }}</small>
                                    </div>
                                    <span class="badge bg-primary fs-6 rounded-3">{{ $nilai->score }}</span>
                                </li>
                            @empty
                                <li class="list-group-item px-4 py-3 text-center text-muted small">Belum ada nilai masuk.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0 pb-4 px-4">
                        <a href="{{ route('siswa.rapor') }}" class="btn btn-outline-primary w-100 rounded-pill btn-sm">Lihat Semua Nilai</a>
                    </div>
                </div>

                {{-- Shortcut E-Rapor --}}
                <div class="card border-0 rounded-4 shadow-sm bg-dark text-white overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <h5 class="fw-bold mb-1">Hasil Belajar</h5>
                        <p class="opacity-75 small mb-3">Lihat detail nilai dan capaian kompetensi siswa.</p>
                        <a href="{{ route('siswa.rapor') }}" class="btn btn-light text-dark fw-bold rounded-pill px-4 shadow-sm">
                            Buka E-Rapor <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                        <i class="fas fa-trophy position-absolute text-warning opacity-25" style="top: -10px; right: -10px; font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary { background: linear-gradient(90deg, #0d6efd 0%, #0a58ca 100%); }
    </style>
</x-app-layout>
