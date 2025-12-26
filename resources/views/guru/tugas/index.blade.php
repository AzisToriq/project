@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">

        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-primary">📚 Manajemen Tugas</h4>
                <p class="text-muted small mb-0">Kelola dan pantau pengumpulan tugas siswa</p>
            </div>

            {{-- TOMBOL LANGSUNG KE CREATE --}}
            <a href="{{ route('guru.tugas.create') }}" class="btn btn-primary rounded-3 px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i> Buat Tugas Baru
            </a>
        </div>

        {{-- Notifikasi Sukses/Error --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div class="ms-2">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Table Section --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-3 rounded-start">Judul Tugas</th>
                        <th class="py-3">Kelas & Mapel</th>
                        <th class="py-3 text-center">Tipe</th>
                        <th class="py-3">Deadline</th>
                        <th class="py-3 pe-3 text-center rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $tugas)
                    <tr>
                        {{-- JUDUL & DESKRIPSI --}}
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $tugas->title }}</div>
                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                {{ $tugas->description ?? 'Tidak ada deskripsi' }}
                            </small>
                        </td>

                        {{-- KELAS & MAPEL --}}
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-1">
                                {{ $tugas->subject->name ?? $tugas->subject->nama_mapel ?? 'Mapel Hapus' }}
                            </span>
                            <div class="small fw-bold text-secondary">
                                {{ $tugas->classroom->name ?? $tugas->classroom->nama_kelas ?? 'Kelas Hapus' }}
                            </div>
                        </td>

                        {{-- TIPE (ONLINE/OFFLINE) --}}
                        <td class="text-center">
                            @if($tugas->type == 'online')
                                <span class="badge rounded-pill bg-info text-white"><i class="fas fa-cloud-upload-alt me-1"></i> Online</span>
                            @else
                                <span class="badge rounded-pill bg-warning text-dark"><i class="fas fa-hand-holding me-1"></i> Offline</span>
                            @endif
                        </td>

                        {{-- DEADLINE --}}
                        <td>
                            @php
                                $deadline = \Carbon\Carbon::parse($tugas->deadline);
                                $isLate = now()->greaterThan($deadline);
                            @endphp
                            <div class="{{ $isLate ? 'text-danger fw-bold' : 'text-dark' }}">
                                {{ $deadline->translatedFormat('d M Y, H:i') }}
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-center pe-3">
                            <div class="btn-group shadow-sm" role="group">
                                {{-- Tombol Detail / Nilai --}}
                                <a href="{{ route('guru.tugas.show', $tugas->id) }}" class="btn btn-sm btn-outline-primary" title="Detail & Nilai">
                                    <i class="fas fa-graduation-cap me-1"></i> Nilai
                                </a>

                                {{-- TOMBOL EDIT BARU --}}
                                <a href="{{ route('guru.tugas.edit', $tugas->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Tugas">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="btnHapus('{{ $tugas->id }}', '{{ $tugas->title }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <form id="form-hapus-{{ $tugas->id }}" action="{{ route('guru.tugas.destroy', $tugas->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                            <p>Belum ada tugas yang dibuat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($assignments, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>

{{-- SweetAlert Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function btnHapus(id, judul) {
        Swal.fire({
            title: 'Hapus Tugas?',
            text: "Tugas '" + judul + "' akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-' + id).submit();
            }
        })
    }
</script>
@endsection
