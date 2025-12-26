@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.index') }}">Tugas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Buat Tugas Baru</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-pen-nib me-2"></i> Form Tugas Baru
                    </h5>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('guru.tugas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 1. PILIH KELAS & MAPEL (GANTI DARI HIDDEN INPUT KE DROPDOWN) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tugas Untuk Kelas</label>
                            <select name="teacher_allocation_id" class="form-select" required>
                                <option value="" disabled {{ !isset($selected_allocation_id) ? 'selected' : '' }}>-- Pilih Kelas & Mapel --</option>
                                @foreach($allocations as $alloc)
                                    <option value="{{ $alloc->id }}" {{ (isset($selected_allocation_id) && $selected_allocation_id == $alloc->id) ? 'selected' : '' }}>
                                        {{ $alloc->classroom->nama_kelas ?? $alloc->classroom->name }} - {{ $alloc->subject->nama_mapel ?? $alloc->subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_allocation_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- 2. JUDUL (name="title" agar sesuai Controller) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Judul Tugas</label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: PR Matematika Bab 1" required>
                            @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- 3. DESKRIPSI (name="description") --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Deskripsi / Soal</label>
                            <textarea name="description" rows="4" class="form-control" placeholder="Tuliskan detail tugas atau instruksi pengerjaan..."></textarea>
                        </div>

                        <div class="row">
                            {{-- 4. TIPE TUGAS (name="type") --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Tipe Pengumpulan</label>
                                <select name="type" class="form-select">
                                    <option value="online">Online (Siswa Upload File)</option>
                                    <option value="offline">Offline (Kumpul di Kelas)</option>
                                </select>
                            </div>

                            {{-- 5. DEADLINE --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Batas Waktu (Deadline)</label>
                                <input type="datetime-local" name="deadline" class="form-control" required>
                                @error('deadline') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- 6. UPLOAD FILE (Opsional, Support Controller) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">File Lampiran (Opsional)</label>
                            <input type="file" name="file" class="form-control">
                            <small class="text-muted">Format: PDF, DOCX, JPG, PNG (Max 5MB)</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('guru.tugas.index') }}" class="btn btn-light text-muted">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="fas fa-paper-plane me-2"></i> Terbitkan Tugas
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
