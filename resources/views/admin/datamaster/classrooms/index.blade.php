@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold">🏫 Data Kelas SD</h1>
            <p class="text-muted small mb-0">Kelola data kelas dan penetapan wali kelas.</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="fas fa-plus me-2"></i> Tambah Kelas
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="px-4 py-3" width="5%">No</th>
                            <th width="15%">Tingkat</th>
                            <th width="20%">Nama Kelas</th>
                            <th width="25%">Wali Kelas</th>
                            <th width="15%">Tahun Ajaran</th>
                            <th class="text-end px-4" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classrooms as $index => $c)
                        <tr>
                            <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 rounded-pill">
                                    Level {{ $c->level }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 1rem;">{{ $c->nama_kelas }}</div>
                                <small class="text-muted">{{ $c->major }}</small>
                            </td>
                            <td>
                                @if($c->waliKelas)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                            <i class="fas fa-user-tie small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $c->waliKelas->nama_lengkap }}</div>
                                            <div class="text-muted" style="font-size: 10px;">NIP: {{ $c->waliKelas->nip }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2">Belum ada</span>
                                @endif
                            </td>
                            <td class="text-muted small fw-bold">{{ $c->academic_year }}</td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="{{ route('guru.data.classrooms.jadwal', $c->id) }}" class="btn btn-sm btn-light text-info border-0 rounded-circle me-1" title="Lihat Jadwal">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>

                                    <button class="btn btn-sm btn-light text-warning border-0 rounded-circle me-1"
                                        onclick="editClass(
                                            {{ $c->id }},
                                            '{{ $c->nama_kelas }}',
                                            '{{ $c->level }}',
                                            '{{ $c->academic_year }}',
                                            '{{ $c->wali_kelas_id }}'
                                        )" title="Edit Kelas">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-light text-danger border-0 rounded-circle"
                                        onclick="deleteClass({{ $c->id }})" title="Hapus Kelas">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $c->id }}" action="{{ route('guru.data.classrooms.destroy', $c->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="80" class="mb-3 opacity-50">
                                <p class="text-muted small">Belum ada data kelas. Silakan tambah baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addClassModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form action="{{ route('guru.data.classrooms.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Kelas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Tingkat (Level) <span class="text-danger">*</span></label>
                        <select name="level" class="form-select" required>
                            @for($i=1; $i<=6; $i++) <option value="{{ $i }}">Kelas {{ $i }}</option> @endfor
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: 1A" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Wali Kelas</label>
                    <select name="wali_kelas_id" class="form-select">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Tahun Ajaran</label>
                    <input type="text" name="academic_year" class="form-control" value="2025/2026" required>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editClassModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form id="editClassForm" method="POST" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-warning text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Data Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Tingkat (Level) <span class="text-danger">*</span></label>
                        <select name="level" id="editLevel" class="form-select" required>
                            @for($i=1; $i<=6; $i++) <option value="{{ $i }}">Kelas {{ $i }}</option> @endfor
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" id="editName" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Wali Kelas</label>
                    <select name="wali_kelas_id" id="editWaliKelas" class="form-select">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Tahun Ajaran</label>
                    <input type="text" name="academic_year" id="editYear" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editClass(id, name, level, year, wali_id) {
        document.getElementById('editName').value = name;
        document.getElementById('editLevel').value = level;
        document.getElementById('editYear').value = year;
        document.getElementById('editWaliKelas').value = wali_id;

        let url = "{{ route('guru.data.classrooms.update', ':id') }}";
        url = url.replace(':id', id);
        document.getElementById('editClassForm').action = url;

        new bootstrap.Modal(document.getElementById('editClassModal')).show();
    }

    function deleteClass(id) {
        Swal.fire({
            title: 'Hapus Kelas?',
            text: "Pastikan tidak ada siswa aktif di dalam kelas ini sebelum menghapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
@endsection
