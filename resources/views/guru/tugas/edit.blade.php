@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.index') }}">Tugas</a></li>
            <li class="breadcrumb-item active">Edit Tugas</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-bold text-warning"><i class="fas fa-edit me-2"></i> Edit Tugas</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('guru.tugas.update', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Dropdown Alokasi --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Kelas & Mata Pelajaran</label>
                            <select name="teacher_allocation_id" class="form-select" required>
                                @foreach($allocations as $alloc)
                                    @php
                                        $selected = ($alloc->classroom_id == $assignment->classroom_id && $alloc->subject_id == $assignment->subject_id) ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $alloc->id }}" {{ $selected }}>
                                        {{ $alloc->classroom->nama_kelas ?? $alloc->classroom->name }} - {{ $alloc->subject->nama_mapel ?? $alloc->subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Judul --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Judul Tugas</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $assignment->title) }}" required>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Instruksi / Deskripsi</label>
                            <textarea name="description" rows="4" class="form-control">{{ old('description', $assignment->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Metode Pengumpulan</label>
                                <select name="type" class="form-select">
                                    <option value="online" {{ $assignment->type == 'online' ? 'selected' : '' }}>Online (Upload File)</option>
                                    <option value="offline" {{ $assignment->type == 'offline' ? 'selected' : '' }}>Offline (Langsung)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Deadline</label>
                                <input type="datetime-local" name="deadline" class="form-control" value="{{ $assignment->deadline->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>

                        {{-- Bagian File Lampiran --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Lampiran File (Opsional)</label>
                            <input type="file" name="file" class="form-control">

                            <div class="mt-3">
                                @if(!empty($assignment->file_path))
                                    <div class="p-3 border rounded-3 bg-light d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                            <i class="fas fa-file-alt fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <small class="d-block text-muted">File Terlampir Saat Ini:</small>
                                            <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="fw-bold text-decoration-none small">
                                                <i class="fas fa-download me-1"></i> Lihat/Download Lampiran
                                            </a>
                                        </div>
                                    </div>
                                    <small class="text-danger mt-1 d-block" style="font-size: 0.75rem">* Abaikan jika tidak ingin mengganti file lama.</small>
                                @else
                                    <div class="p-2 border border-dashed rounded-3 text-center">
                                        <small class="text-muted fst-italic">Belum ada lampiran file untuk tugas ini.</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('guru.tugas.index') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-warning px-4 fw-bold">Update Tugas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
