<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Orang Tua - Kelas {{ $classroom->nama_kelas }}
        </h2>
    </x-slot>

    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('guru.data.parents.index') }}" class="btn btn-link text-secondary p-0 mb-2 text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Kelas
                </a>
                <h1 class="h3 text-gray-800 font-weight-bold">
                    Data Orang Tua ({{ $classroom->nama_kelas }})
                </h1>
                <p class="text-muted small mb-0">Daftar akun orang tua yang memiliki anak di kelas ini.</p>
            </div>

            {{-- Shortcut ke Data Siswa (Solusi biar ga bingung nambah link) --}}
            <a href="{{ route('guru.data.students.show_class', $classroom->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-link me-2"></i> Tautkan Ortu Baru (via Data Siswa)
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="px-4 py-3" style="width: 50px;">No</th>
                                <th class="py-3">Nama Orang Tua</th>
                                <th class="py-3">Akun Login</th>
                                <th class="py-3">Siswa (Anak)</th>
                                <th class="text-end px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parents as $index => $p)
                            <tr>
                                <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-warning bg-opacity-10 text-warning me-3 d-flex align-items-center justify-content-center rounded-circle fw-bold" style="width: 40px; height: 40px;">
                                            {{ substr($p->name, 0, 1) }}
                                        </div>
                                        <span class="fw-bold text-dark">{{ $p->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark"><i class="fas fa-user me-1 text-muted"></i> {{ $p->username }}</div>
                                    <div class="small text-muted"><i class="fas fa-envelope me-1"></i> {{ $p->email }}</div>
                                </td>
                                <td>
                                    {{-- Loop Anak --}}
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($p->students as $child)
                                            <span class="badge {{ $child->classroom_id == $classroom->id ? 'bg-primary' : 'bg-secondary' }} bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill fw-normal">
                                                {{ $child->nama_lengkap }}
                                                @if($child->classroom_id != $classroom->id)
                                                    <span class="text-muted ms-1">({{ $child->classroom->nama_kelas }})</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-end px-4">
                                    {{-- TOMBOL EDIT --}}
                                    <button class="btn btn-sm btn-light text-warning border-0 rounded-circle me-1"
                                            onclick='openEditModal(@json($p))' title="Edit Akun">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    {{-- TOMBOL HAPUS --}}
                                    <button class="btn btn-sm btn-light text-danger border-0 rounded-circle"
                                            onclick="deleteParent({{ $p->id }})" title="Hapus Akun">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>

                                    {{-- Form Hapus (Hidden) --}}
                                    <form id="delete-parent-{{ $p->id }}" action="{{ route('guru.data.parents.destroy', $p->id) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://img.freepik.com/free-vector/no-data-concept-illustration_114360-536.jpg" alt="No Data" style="width: 150px; opacity: 0.6;">
                                    <p class="text-muted small mt-2">Belum ada data orang tua di kelas ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT AKUN ORANG TUA --}}
    <div class="modal fade" id="editParentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="editParentForm" method="POST" class="modal-content border-0 shadow-lg">
                @csrf @method('PUT')

                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold" id="editModalTitle">Edit Akun Orang Tua</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username Login</label>
                        <input type="text" name="username" id="edit_username" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3" required>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">Ubah Password (Opsional)</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="form-text small">Minimal 6 karakter jika diisi.</div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inisialisasi Modal Edit
        const editModalElement = document.getElementById('editParentModal');
        const editModal = new bootstrap.Modal(editModalElement);
        const editForm = document.getElementById('editParentForm');

        // Fungsi Buka Modal Edit & Isi Data
        function openEditModal(data) {
            // Set URL Update dinamis berdasarkan ID
            let url = "{{ route('guru.data.parents.update', ':id') }}";
            editForm.action = url.replace(':id', data.id);

            // Isi field input dengan data yang diklik
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_username').value = data.username;
            document.getElementById('edit_email').value = data.email;

            // Tampilkan Modal
            editModal.show();
        }

        // Fungsi Hapus dengan Konfirmasi SweetAlert
        function deleteParent(id) {
            Swal.fire({
                title: 'Hapus Akun?',
                text: "Akun orang tua ini akan dihapus permanen! Data siswa tidak akan terhapus, hanya statusnya menjadi 'belum ditautkan'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form hapus yang hidden
                    document.getElementById('delete-parent-' + id).submit();
                }
            });
        }

        // Notifikasi Sukses dari Controller
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        // Notifikasi Error
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "Periksa kembali inputan Anda. Username/Email mungkin sudah terpakai."
            });
        @endif
    </script>
    @endpush
</x-app-layout>
