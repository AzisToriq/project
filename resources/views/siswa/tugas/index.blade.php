@extends('layouts.app')

@section('content')
<div class="py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
            <i class="fas fa-tasks fa-lg"></i>
        </div>
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold mb-0">Tugas & PR Saya</h1>
            <p class="text-muted small mb-0">Daftar tugas yang harus kamu kerjakan.</p>
        </div>
    </div>

    {{-- Daftar Tugas --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3">Mata Pelajaran</th>
                            <th>Judul Tugas</th>
                            <th>Tipe</th>
                            <th>Deadline</th>
                            <th class="text-center">Status</th>
                            <th class="text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $tugas)
                            <tr>
                                {{-- Kolom 1: Mapel & Guru (FIX RELASI) --}}
                                <td class="px-4">
                                    <div class="fw-bold text-dark">
                                        {{ $tugas->subject->nama_mapel ?? $tugas->subject->name ?? 'Mapel Umum' }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-user-tie me-1"></i>
                                        {{ $tugas->teacher->nama_lengkap ?? $tugas->teacher->name ?? 'Guru Pengampu' }}
                                    </small>
                                </td>

                                {{-- Kolom 2: Judul (FIX KOLOM) --}}
                                <td>
                                    <div class="fw-bold text-primary">{{ $tugas->title }}</div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                        {{ $tugas->description ?? 'Tidak ada deskripsi detail.' }}
                                    </small>
                                </td>

                                {{-- Kolom 3: Tipe (FIX KOLOM) --}}
                                <td class="text-nowrap">
                                    @if($tugas->type == 'online')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">Online</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3">Offline</span>
                                    @endif
                                </td>

                                {{-- Kolom 4: Deadline --}}
                                <td>
                                    @php
                                        $deadline = \Carbon\Carbon::parse($tugas->deadline);
                                        $isLate = now()->greaterThan($deadline);
                                        $sisa = now()->diff($deadline);
                                    @endphp

                                    <div class="{{ $isLate ? 'text-danger fw-bold' : 'text-dark' }}">
                                        {{ $deadline->translatedFormat('d M Y, H:i') }}
                                    </div>

                                    @if(!$isLate)
                                        <small class="text-success fw-bold">
                                            {{ $sisa->days > 0 ? $sisa->days . ' hari lagi' : $sisa->h . ' jam lagi' }}
                                        </small>
                                    @else
                                        <small class="text-danger bg-danger bg-opacity-10 px-2 rounded">Waktu Habis</small>
                                    @endif
                                </td>

                                {{-- Kolom 5: Status Selesai (FIX RELASI) --}}
                                <td class="text-center">
                                    @php
                                        // Ambil submission milik siswa login saja dari koleksi submissions di tugas
                                        $mySub = $tugas->submissions->first();
                                        $isSubmitted = !is_null($mySub);
                                        $grade = $isSubmitted ? $mySub->grade : null;
                                    @endphp

                                    @if($isSubmitted && !is_null($grade))
                                        <span class="badge bg-primary rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-check-double me-1"></i> Nilai: {{ $grade }}
                                        </span>
                                    @elseif($isSubmitted)
                                        <span class="badge bg-success rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-check me-1"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3">
                                            Belum
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom 6: Tombol Aksi --}}
                                <td class="text-end px-4">
                                    <a href="{{ route('siswa.tugas.show', $tugas->id) }}" class="btn btn-sm {{ $isSubmitted ? 'btn-outline-success' : 'btn-primary' }} rounded-pill px-3 shadow-sm">
                                        {{ $isSubmitted ? 'Detail' : 'Kerjakan' }} <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" width="80" class="mb-3 opacity-50" alt="Empty">
                                    <h6 class="text-dark fw-bold">Hore! Tidak Ada Tugas</h6>
                                    <p class="text-muted small mb-0">Kamu bebas tugas untuk saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
