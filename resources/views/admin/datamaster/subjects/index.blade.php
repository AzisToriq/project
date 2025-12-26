@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold">📚 Data Mata Pelajaran</h1>
            <p class="text-muted small mb-0">Kelola daftar mata pelajaran yang diajarkan.</p>
        </div>

        @if(Auth::user()->role === 'admin')
        <button class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="fas fa-plus me-2"></i> Tambah Mapel
        </button>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="px-4 py-3" width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th width="40%">Nama Mata Pelajaran</th>

                            @if(Auth::user()->role === 'admin')
                            <th class="text-end px-4" width="20%">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $index => $s)
                        <tr>
                            <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2">
                                    {{ $s->code }}
                                </span>
                            </td>
                            <td class="fw-bold text-dark">{{ $s->nama_mapel }}</td>

                            @if(Auth::user()->role === 'admin')
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light text-warning border-0 rounded-circle me-1"
                                        onclick="editSubject({{ $s->id }}, '{{ $s->nama_mapel }}', '{{ $s->code }}')" title="Edit Mapel">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-light text-danger border-0 rounded-circle"
                                        onclick="deleteSubject({{ $s->id }})" title="Hapus Mapel">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $s->id }}" action="{{ route('guru.data.subjects.destroy', $s->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'admin' ? 4 : 3 }}" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="80" class="mb-3 opacity-50">
                                <p class="text-muted small">Belum ada data mata pelajaran.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->role === 'admin')
<div class="modal fade" id="addSubjectModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form action="{{ route('guru.data.subjects.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Mapel Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Mapel <span class="text-danger">*</span></label>
                    <input type="text" name="nama_mapel" class="form-control" required placeholder="Contoh: Matematika">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Kode Mapel <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" required placeholder="Contoh: MTK">
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editSubjectModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form id="editSubjectForm" method="POST" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-warning text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Mapel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Mapel <span class="text-danger">*</span></label>
                    <input type="text" name="nama_mapel" id="editName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Kode Mapel <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="editCode" class="form-control" required>
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
    function editSubject(id, name, code) {
        document.getElementById('editName').value = name;
        document.getElementById('editCode').value = code;

        let url = "{{ route('guru.data.subjects.update', ':id') }}";
        url = url.replace(':id', id);
        document.getElementById('editSubjectForm').action = url;

        new bootstrap.Modal(document.getElementById('editSubjectModal')).show();
    }

    function deleteSubject(id) {
        Swal.fire({
            title: 'Hapus Mapel?',
            text: "Jadwal dan nilai terkait mapel ini mungkin akan terpengaruh!",
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
@endif
@endsection
