@extends('layouts.app')

@section('content')
<div class="py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('siswa.tugas.index') }}">Daftar Tugas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Tugas</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        {{-- FIX RELASI SUBJECT --}}
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill me-2">
                            {{ $assignment->subject->nama_mapel ?? $assignment->subject->name ?? 'Mata Pelajaran' }}
                        </span>
                        {{-- FIX KOLOM TYPE --}}
                        <span class="badge {{ $assignment->type == 'online' ? 'bg-info' : 'bg-warning' }} bg-opacity-10 {{ $assignment->type == 'online' ? 'text-info' : 'text-dark' }} px-3 py-2 rounded-pill">
                            <i class="fas {{ $assignment->type == 'online' ? 'fa-globe' : 'fa-hand-holding' }} me-1"></i>
                            Tugas {{ ucfirst($assignment->type ?? 'offline') }}
                        </span>
                    </div>

                    {{-- FIX KOLOM TITLE --}}
                    <h3 class="fw-bold text-dark mb-3">{{ $assignment->title }}</h3>

                    <div class="p-3 bg-light rounded-3 mb-4 text-secondary" style="min-height: 150px; line-height: 1.6;">
                        <strong>Instruksi Tugas:</strong><br>
                        {{-- FIX KOLOM DESCRIPTION --}}
                        {!! nl2br(e($assignment->description ?? 'Tidak ada deskripsi detail.')) !!}
                    </div>

                    {{-- FILE LAMPIRAN DARI GURU (FITUR BARU) --}}
                    @if($assignment->file_path)
                    <div class="mb-4">
                        <label class="small fw-bold text-dark d-block mb-2">Lampiran Materi/Soal dari Guru:</label>
                        <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3">
                            <i class="fas fa-download me-1"></i> Download Lampiran Guru
                        </a>
                    </div>
                    @endif

                    <div class="row text-center border-top pt-3">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Batas Waktu</small>
                            <span class="fw-bold text-danger">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Guru Pengampu</small>
                            <span class="fw-bold text-dark">
                                {{-- FIX RELASI TEACHER --}}
                                <i class="fas fa-user-tie me-1"></i> {{ $assignment->teacher->nama_lengkap ?? $assignment->teacher->name ?? 'Guru' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- BOX NILAI --}}
            @if($submission && $submission->grade !== null)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-start border-primary border-4">
                    <div class="card-body p-4 text-center">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="letter-spacing: 1px;">Nilai Hasil Kerja</small>
                        <h1 class="display-4 fw-bold {{ $submission->grade >= 75 ? 'text-success' : 'text-danger' }} mb-0">
                            {{ $submission->grade }}
                        </h1>
                        <hr class="my-3">
                        <div class="text-start">
                            <label class="small fw-bold text-primary"><i class="fas fa-comment-dots me-1"></i> Feedback Guru:</label>
                            <div class="p-2 bg-light rounded border small text-dark mt-1 italic">
                                "{{ $submission->feedback ?? 'Terima kasih sudah mengerjakan.' }}"
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- BOX STATUS PENGUMPULAN --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-upload me-2 text-primary"></i>Status Tugas</h6>
                </div>
                <div class="card-body p-4 pt-0">

                    @if($submission)
                        <div class="text-center py-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-4 p-3 mb-3">
                                <i class="fas fa-check-circle fa-3x mb-2"></i>
                                <h6 class="fw-bold mb-0">Sudah Dikumpulkan</h6>
                                <small class="opacity-75">{{ $submission->updated_at->translatedFormat('d M, H:i') }}</small>
                            </div>

                            @if($submission->file_path)
                                <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-2">
                                    <i class="fas fa-file-download me-1"></i> Lihat File Saya
                                </a>
                            @endif

                            @if($submission->grade === null && now() < $assignment->deadline)
                                <button class="btn btn-light btn-sm w-100 rounded-pill text-muted shadow-sm" data-bs-toggle="collapse" data-bs-target="#formSubmission">
                                    <i class="fas fa-edit me-1"></i> Edit Pengumpulan
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning border-0 rounded-4 text-center mb-3">
                            <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                            <p class="small fw-bold mb-0">Kamu belum mengumpulkan tugas ini.</p>
                        </div>
                    @endif

                    <div class="collapse {{ !$submission ? 'show' : '' }} mt-2" id="formSubmission">
                        @if(now() < $assignment->deadline)
{{-- PASTIKAN SEPERTI INI --}}
<form action="{{ route('siswa.tugas.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                {{-- FIX KOLOM TYPE --}}
                                @if($assignment->type == 'online')
                                    <div class="mb-3">
                                        <label class="small fw-bold mb-1">Pilih File {{ $submission ? '(Ganti File)' : 'Jawaban' }}</label>
                                        <input type="file" name="file" class="form-control form-control-sm rounded-3">
                                        @if(!$submission) <small class="text-danger" style="font-size: 9px;">*Wajib diisi (PDF/JPG/DOCX)</small> @endif
                                    </div>
                                @else
                                    <div class="bg-light p-3 rounded-3 mb-3 text-center border">
                                        <i class="fas fa-info-circle text-primary mb-1"></i>
                                        <p class="small mb-0 text-dark">Tugas ini dikumpulkan secara <strong>Offline</strong> di kelas.</p>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Catatan Ke Guru</label>
                                    {{-- FIX KOLOM NOTES (PAKAI S) --}}
                                    <textarea name="notes" class="form-control form-control-sm rounded-3" rows="2" placeholder="Tulis catatan jika ada...">{{ $submission->notes ?? '' }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm mb-2">
                                    <i class="fas fa-paper-plane me-1"></i>
                                    {{ $submission ? 'Simpan Perubahan' : 'Kirim Jawaban' }}
                                </button>
                            </form>

                            @if($submission && $submission->grade === null)
                                <form id="form-hapus-tugas" action="{{ route('siswa.tugas.delete', $assignment->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button" onclick="confirmCancelSubmission()" class="btn btn-link text-danger w-100 small text-decoration-none mt-2">
                                    <i class="fas fa-trash-alt me-1"></i> Batalkan Pengumpulan
                                </button>
                            @endif

                        @else
                            <div class="text-center text-danger py-2">
                                <i class="fas fa-lock me-1"></i> <strong>Batas Waktu Berakhir</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmCancelSubmission() {
        Swal.fire({
            title: 'Batalkan Pengumpulan?',
            text: "Jawaban kamu akan dihapus. Kamu harus mengirim ulang sebelum deadline!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tetap Simpan'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-tugas').submit();
            }
        })
    }
</script>
@endpush
