@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('guru.nilai.mapel', $allocation->classroom_id) }}" class="btn btn-link text-secondary p-0 mb-2 text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
            <h1 class="h3 text-gray-800 font-weight-bold">📈 Leger Nilai (Rekap)</h1>
            <p class="text-muted small mb-0">
                Mapel: <b>{{ $allocation->subject->name }}</b> | Kelas: <b>{{ $allocation->classroom->name }}</b>
            </p>
        </div>
        <button class="btn btn-success shadow-sm rounded-pill px-4" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Cetak Leger
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="bg-warning text-dark text-center">
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">No</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: left;" class="px-4">Nama Siswa</th>
                            <th colspan="4">Rincian Nilai</th>
                            <th rowspan="2" style="vertical-align: middle;">Nilai Akhir</th>
                            <th rowspan="2" style="vertical-align: middle;">Predikat</th>
                        </tr>
                        <tr>
                            <th>UH 1</th>
                            <th>UH 2</th>
                            <th>UTS</th>
                            <th>UAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $s)
                        @php
                            $n = $gradeMap[$s->id] ?? [];
                            $uh1 = $n['uh1'] ?? 0;
                            $uh2 = $n['uh2'] ?? 0;
                            $uts = $n['uts'] ?? 0;
                            $uas = $n['uas'] ?? 0;

                            // Hitung Rata-rata (NA)
                            $total = $uh1 + $uh2 + $uts + $uas;
                            $na = ($total > 0) ? round($total / 4) : 0; // Pembagi 4 (Sederhana)

                            // Predikat
                            if($na >= 90) $pred = 'A';
                            elseif($na >= 80) $pred = 'B';
                            elseif($na >= 70) $pred = 'C';
                            else $pred = 'D';
                        @endphp
                        <tr class="text-center">
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start px-4 fw-bold">{{ $s->name }}</td>
                            <td>{{ $uh1 ?: '-' }}</td>
                            <td>{{ $uh2 ?: '-' }}</td>
                            <td>{{ $uts ?: '-' }}</td>
                            <td>{{ $uas ?: '-' }}</td>
                            <td class="fw-bold bg-light">{{ $na }}</td>
                            <td>
                                <span class="badge {{ $na >= 70 ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                                    {{ $pred }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4">Belum ada data nilai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
