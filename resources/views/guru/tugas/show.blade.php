@extends('layouts.app')

@section('content')
<div class="py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.index') }}">Manajemen Tugas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Pengumpulan</li>
        </ol>
    </nav>

    {{-- Info Tugas Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    {{-- Judul Tugas --}}
                    <h4 class="fw-bold text-primary mb-1">{{ $assignment->title }}</h4>

                    {{-- Info Kelas & Mapel --}}
                    <p class="text-muted mb-2">
                        {{ $assignment->classroom->nama_kelas ?? 'Kelas Hapus' }} &bull;
                        {{ $assignment->subject->nama_mapel ?? 'Mapel Hapus' }}
                    </p>

                    {{-- Badge Tipe (Jika kolom 'type' ada di DB, tampilkan. Jika tidak, hapus blok ini) --}}
                    @if(isset($assignment->type))
                        @if($assignment->type == 'online')
                            <span class="badge bg-purple-100 text-purple-800 border border-purple-200">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Tipe Online
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">
                                <i class="fas fa-book me-1"></i> Tipe Offline
                            </span>
                        @endif
                    @endif
                </div>

                {{-- Deadline --}}
                <div class="text-end">
                    <small class="text-muted d-block">Deadline</small>
                    <span class="fw-bold text-danger fs-5">
                        {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>

            <hr class="my-3">

            {{-- Deskripsi Tugas --}}
            <div class="small text-secondary">
                <strong>Deskripsi:</strong> <br>
                {!! nl2br(e($assignment->description ?? '-')) !!}
            </div>
        </div>
    </div>

    {{-- Tabel Pengumpulan Siswa --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">Daftar Pengumpulan Siswa</h6>
            <span class="badge bg-primary rounded-pill">{{ count($submissions) }} / {{ count($students) }} Mengumpulkan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Nilai</th>
                            {{-- Kolom File (Hanya jika tipe online / ada file path) --}}
                            <th>File / Waktu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            @php
                                $sub = $submissions[$student->id] ?? null;
                            @endphp
                            <tr>
                                <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>

                                {{-- Nama Siswa & Catatan --}}
                                <td>
                                    <div class="fw-bold text-dark">{{ $student->nama_lengkap }}</div>
                                    <small class="text-muted fst-italic">
                                        {{ $sub ? ($sub->notes ?? 'Tanpa Catatan') : '' }}
                                    </small>
                                </td>

                                {{-- Status Pengumpulan --}}
                                <td class="text-center">
                                    @if($sub)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Sudah</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Belum</span>
                                    @endif
                                </td>

                                {{-- Nilai --}}
                                <td class="text-center">
                                    <span class="fw-bold fs-5 {{ ($sub->grade ?? 0) < 70 ? 'text-danger' : 'text-primary' }}">
                                        {{ $sub->grade ?? '-' }}
                                    </span>
                                </td>

                                {{-- File Download --}}
                                <td>
                                    @if($sub && $sub->file_path)
                                        <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="btn btn-xs btn-link p-0 text-decoration-none fw-bold">
                                            <i class="fas fa-file-download me-1"></i> Unduh
                                        </a>
                                        <div class="small {{ $sub->created_at > $assignment->deadline ? 'text-danger' : 'text-muted' }}" style="font-size: 10px;">
                                            {{ $sub->created_at->format('d/m H:i') }}
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                {{-- Aksi (Nilai) --}}
                                <td class="text-center">
                                    @if($sub)
                                        <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNilai{{ $sub->id }}">
                                            <i class="fas fa-edit me-1"></i> Nilai
                                        </button>

                                        {{-- MODAL INPUT NILAI --}}
                                        <div class="modal fade" id="modalNilai{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-4 text-start">
                                                    <form action="{{ route('guru.tugas.grade', $sub->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header bg-light border-0">
                                                            <h5 class="modal-title fw-bold">Input Nilai: {{ $student->nama_lengkap }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            {{-- Info Catatan Siswa --}}
                                                            <div class="mb-3 text-center bg-light p-3 rounded-3">
                                                                <label class="small text-muted d-block mb-1">Catatan Siswa:</label>
                                                                <span class="fst-italic">"{{ $sub->notes ?? 'Tidak ada catatan' }}"</span>
                                                            </div>

                                                            {{-- Input Nilai --}}
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small">Nilai (0-100)</label>
                                                                <input type="number" name="grade" class="form-control form-control-lg text-center fw-bold text-primary" value="{{ $sub->grade }}" min="0" max="100" required>
                                                            </div>

                                                            {{-- Input Feedback (Opsional, pastikan kolom feedback ada di DB) --}}
                                                            <div class="mb-0">
                                                                <label class="form-label fw-bold small">Feedback / Komentar Guru</label>
                                                                <textarea name="feedback" class="form-control" rows="3" placeholder="Contoh: Kerja bagus! Pertahankan.">{{ $sub->feedback ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 p-4 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Nilai</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-light disabled rounded-pill px-3">Menunggu</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Tidak ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-purple-100 { background-color: #f3e8ff; }
    .text-purple-800 { color: #6b21a8; }
    .border-purple-200 { border-color: #e9d5ff; }
    .btn-xs { padding: 0.1rem 0.4rem; font-size: 0.75rem; }
</style>
@endsection
