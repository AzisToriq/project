<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Siswa - Kelas {{ $classroom->nama_kelas }}
        </h2>
    </x-slot>

    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('guru.data.students.index') }}" class="btn btn-link text-secondary p-0 mb-2 text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Direktori
                </a>
                <h1 class="h3 text-gray-800 font-weight-bold">
                    🎓 Siswa {{ $classroom->nama_kelas }}
                </h1>
                <p class="text-muted small mb-0">Kelola data lengkap siswa, akun, dan data orang tua.</p>
            </div>
            <button class="btn btn-primary rounded-pill shadow-sm px-4" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i> Tambah Siswa
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="px-4 py-3" style="width: 50px;">No</th>
                                <th class="py-3">Identitas Siswa</th>
                                <th class="py-3">Kontak & Alamat</th>
                                <th class="py-3">Data Orang Tua</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="text-end px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $s)
                            <tr>
                                <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3 d-flex align-items-center justify-content-center rounded-circle fw-bold" style="width: 40px; height: 40px;">
                                            {{ substr($s->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $s->nama_lengkap }}</div>
                                            <div class="small text-muted">
                                                NISN: {{ $s->nisn }} | {{ $s->jenis_kelamin }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-dark"><i class="fas fa-phone me-1 text-muted"></i> {{ $s->nomor_telepon ?? '-' }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 150px;">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $s->alamat ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    {{-- Menampilkan Nama Akun Orang Tua Jika Ada --}}
                                    @if($s->parent)
                                        <div class="small fw-bold text-primary">
                                            <i class="fas fa-link me-1"></i> {{ $s->parent->name }}
                                        </div>
                                    @else
                                        <div class="small text-muted fst-italic">Belum ditautkan</div>
                                    @endif
                                    <div class="small text-muted mt-1 border-top pt-1">
                                        Ayah: {{ $s->nama_ayah ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($s->user && $s->user->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group">
                                        <a href="{{ route('guru.data.students.rapor', $s->id) }}" class="btn btn-sm btn-light text-info border-0 rounded-circle me-1" title="Lihat Rapor">
                                            <i class="fas fa-file-alt"></i>
                                        </a>

                                        {{-- Tombol Edit dengan JSON Data --}}
                                        <button class="btn btn-sm btn-light text-warning border-0 rounded-circle me-1"
                                                onclick='openEditModal(@json($s))' title="Edit Data Lengkap">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>

                                        <button class="btn btn-sm btn-light text-danger border-0 rounded-circle"
                                                onclick="deleteStudent({{ $s->id }})" title="Hapus Siswa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <form id="delete-student-{{ $s->id }}" action="{{ route('guru.data.students.destroy', $s->id) }}" method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://img.freepik.com/free-vector/no-data-concept-illustration_114360-536.jpg" alt="No Data" style="width: 150px; opacity: 0.6;">
                                    <p class="text-muted small mt-2">Belum ada data siswa.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GLOBAL --}}
    <div class="modal fade" id="studentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form id="studentForm" method="POST" class="modal-content border-0 shadow-lg">
                @csrf
                <div id="methodField"></div> {{-- Untuk @method('PUT') saat edit --}}
                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Siswa Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <ul class="nav nav-tabs nav-justified bg-light" id="studentTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pribadi" type="button">1. Data Pribadi</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-alamat" type="button">2. Alamat</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ortu" type="button">3. Orang Tua</button>
                        </li>
                    </ul>

                    <div class="tab-content p-4">
                        {{-- TAB 1: PRIBADI --}}
                        <div class="tab-pane fade show active" id="tab-pribadi">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">NISN (Username Login) <span class="text-danger">*</span></label>
                                    <input type="text" name="nisn" id="nisn" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">NIS</label>
                                    <input type="text" name="nis" id="nis" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">NIK</label>
                                    <input type="text" name="nik" id="nik" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Agama</label>
                                    <select name="agama" id="agama" class="form-select">
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: ALAMAT --}}
                        <div class="tab-pane fade" id="tab-alamat">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Lengkap</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Provinsi</label>
                                    <input type="text" name="provinsi" id="provinsi" class="form-control" value="Lampung">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Kabupaten/Kota</label>
                                    <input type="text" name="kabupaten" id="kabupaten" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Kecamatan</label>
                                    <input type="text" name="kecamatan" id="kecamatan" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Kode Pos</label>
                                    <input type="text" name="kode_pos" id="kode_pos" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nomor Telepon / WA</label>
                                <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control">
                            </div>
                        </div>

                        {{-- TAB 3: ORANG TUA --}}
                        <div class="tab-pane fade" id="tab-ortu">
                            <div class="alert alert-info border-0 small bg-info bg-opacity-10 text-info-emphasis mb-3">
                                <i class="fas fa-info-circle me-1"></i> Tautkan akun orang tua agar data siswa muncul di dashboard mereka.
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-primary">Tautkan Akun Wali Murid</label>
                                <select name="parent_user_id" id="parent_user_id" class="form-select border-primary bg-light">
                                    <option value="">-- Tidak Ada / Lepas Tautan --</option>
                                    @foreach($parents as $ortu)
                                        <option value="{{ $ortu->id }}">{{ $ortu->name }} ({{ $ortu->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr>

                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-male me-2"></i>Biodata Ayah</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nama Ayah</label>
                                    <input type="text" name="nama_ayah" id="nama_ayah" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Pekerjaan Ayah</label>
                                    <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control">
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-3 mt-3"><i class="fas fa-female me-2"></i>Biodata Ibu</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nama Ibu</label>
                                    <input type="text" name="nama_ibu" id="nama_ibu" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Pekerjaan Ibu</label>
                                    <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control">
                                </div>
                            </div>

                            {{-- Fitur Reset Password hanya muncul saat Edit --}}
                            <div id="resetPasswordContainer" class="mt-4 pt-3 border-top d-none">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="reset_password" name="reset_password" value="1">
                                    <label class="form-check-label text-danger fw-bold" for="reset_password">Reset Password Siswa ke Default ('password123')</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const modalElement = document.getElementById('studentModal');
        const modal = new bootstrap.Modal(modalElement);
        const form = document.getElementById('studentForm');
        const title = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');
        const resetPassContainer = document.getElementById('resetPasswordContainer');

        function openAddModal() {
            form.reset();
            form.action = "{{ route('guru.data.students.store') }}";
            title.innerText = "Tambah Siswa Baru";
            title.parentElement.classList.remove('bg-warning');
            title.parentElement.classList.add('bg-primary');
            methodField.innerHTML = '';
            resetPassContainer.classList.add('d-none');

            // Reset select2 atau dropdown manual jika ada
            document.getElementById('parent_user_id').value = "";

            modal.show();
        }

        function openEditModal(data) {
            form.reset();

            // Set Action URL
            let url = "{{ route('guru.data.students.update', ':id') }}";
            form.action = url.replace(':id', data.id);

            // Tampilan Modal Edit
            title.innerText = "Edit Data Siswa: " + data.nama_lengkap;
            title.parentElement.classList.remove('bg-primary');
            title.parentElement.classList.add('bg-warning');

            // Tambahkan Method PUT
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Tampilkan opsi reset password
            resetPassContainer.classList.remove('d-none');

            // Isi Data ke Form (Mapping Manual)
            document.getElementById('nama_lengkap').value = data.nama_lengkap;
            document.getElementById('nisn').value = data.nisn;
            document.getElementById('nis').value = data.nis;
            document.getElementById('nik').value = data.nik;
            document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
            document.getElementById('tempat_lahir').value = data.tempat_lahir;
            document.getElementById('tanggal_lahir').value = data.tanggal_lahir;
            document.getElementById('agama').value = data.agama;
            document.getElementById('email').value = data.email;

            document.getElementById('alamat').value = data.alamat;
            document.getElementById('provinsi').value = data.provinsi;
            document.getElementById('kabupaten').value = data.kabupaten;
            document.getElementById('kecamatan').value = data.kecamatan;
            document.getElementById('kode_pos').value = data.kode_pos;
            document.getElementById('nomor_telepon').value = data.nomor_telepon;

            // Isi Data Orang Tua & Akun Parent
            document.getElementById('parent_user_id').value = data.parent_user_id; // Dropdown
            document.getElementById('nama_ayah').value = data.nama_ayah;
            document.getElementById('pekerjaan_ayah').value = data.pekerjaan_ayah;
            document.getElementById('nama_ibu').value = data.nama_ibu;
            document.getElementById('pekerjaan_ibu').value = data.pekerjaan_ibu;

            modal.show();
        }

        function deleteStudent(id) {
            Swal.fire({
                title: 'Hapus Siswa?',
                text: "Data siswa, akun login, dan nilai akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-student-' + id).submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
