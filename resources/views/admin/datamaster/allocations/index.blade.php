@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h3 text-gray-800 font-weight-bold">🧩 Plotting Guru & Mapel</h1>
            <p class="text-muted small mb-0">Atur distribusi guru pengajar untuk setiap mata pelajaran per kelas.</p>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
                <div class="input-group shadow-sm" style="max-width: 250px;">
                    <span class="input-group-text bg-white border-end-0 ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchClass" class="form-control border-start-0 ps-2" placeholder="Cari Kelas...">
                </div>
                <button class="btn btn-primary rounded-pill shadow-sm fw-bold px-4" data-bs-toggle="modal" data-bs-target="#addAllocationModal">
                    <i class="fas fa-plus me-2"></i> Tambah
                </button>
            </div>
        </div>
    </div>

    <div class="row" id="plottingContainer">
        @forelse($classrooms as $class)
        <div class="col-xl-4 col-lg-6 mb-4 class-item">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1 class-name">{{ $class->nama_kelas }}</h5>
                        <span class="badge bg-light text-secondary border">Level {{ $class->level }}</span>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="px-4 mb-2">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Daftar Pengajar</small>
                    </div>

                    @if($class->teacher_allocations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($class->teacher_allocations as $plot)
                            <div class="list-group-item border-0 px-4 py-2 d-flex align-items-center justify-content-between hover-bg-light">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <div class="avatar-circle bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width: 35px; height: 35px; font-size: 0.8rem; font-weight: bold;">
                                        {{ substr($plot->teacher->nama_lengkap ?? '?', 0, 1) }}
                                    </div>
                                    <div class="text-truncate">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;">
                                            {{ $plot->subject->nama_mapel }}
                                        </div>
                                        <div class="small text-muted text-truncate">
                                            {{ $plot->teacher->nama_lengkap ?? 'Guru Terhapus' }}
                                        </div>
                                    </div>
                                </div>

                                <button onclick="deleteItem({{ $plot->id }})" class="btn btn-link text-danger p-0 ms-2 opacity-50 hover-opacity-100" title="Hapus">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                                <form id="delete-form-{{ $plot->id }}" action="{{ route('guru.data.allocations.destroy', $plot->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 opacity-50">
                            <i class="fas fa-clipboard-list fa-3x mb-3 text-gray-300"></i>
                            <p class="small text-muted">Belum ada guru yang di-plot.</p>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light border-0 py-2 px-4">
                    <div class="d-flex justify-content-between align-items-center small">
                        <span class="text-muted">Total Mapel:</span>
                        <span class="fw-bold text-dark">{{ $class->teacher_allocations->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="alert alert-warning d-inline-block px-5 py-4 rounded-4 shadow-sm">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h5>Belum Ada Kelas</h5>
                <p class="mb-0">Silakan tambahkan data kelas terlebih dahulu.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div id="noResult" class="d-none text-center py-5">
        <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
        <p class="text-muted">Kelas tidak ditemukan.</p>
    </div>
</div>

<div class="modal fade" id="addAllocationModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form action="{{ route('guru.data.allocations.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Pembagian Tugas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="mb-3">
                    <label class="form-label small fw-bold">1. Pilih Kelas</label>
                    <select name="classroom_id" class="form-select bg-light" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">2. Pilih Mata Pelajaran</label>
                    <select name="subject_id" class="form-select bg-light" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_mapel }} ({{ $s->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">3. Pilih Guru Pengajar</label>
                    <select name="teacher_id" class="form-select bg-light" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->nama_lengkap }} ({{ $t->nip }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="alert alert-light border small text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Guru yang dipilih akan bertanggung jawab menginput nilai untuk mapel ini di kelas tersebut.
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT SEARCH & DELETE --}}
@push('scripts')
<script>
    // Fitur Search Cepat
    document.getElementById('searchClass').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.class-item');
        let visibleCount = 0;

        items.forEach(function(item) {
            let name = item.querySelector('.class-name').innerText.toLowerCase();
            if (name.includes(filter)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        let noResult = document.getElementById('noResult');
        if (visibleCount === 0 && filter !== '') noResult.classList.remove('d-none');
        else noResult.classList.add('d-none');
    });

    // Fitur Delete dengan SweetAlert
    function deleteItem(id) {
        Swal.fire({
            title: 'Hapus Plotting?',
            text: "Guru ini tidak akan mengajar mapel ini lagi di kelas tersebut. Nilai yang sudah diinput mungkin akan terlepas.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-'+id).submit();
            }
        });
    }
</script>

{{-- CSS Tambahan --}}
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
    .hover-opacity-100:hover {
        opacity: 1 !important;
    }
    .avatar-circle {
        font-family: 'Arial', sans-serif;
    }
</style>
@endpush
@endsection
