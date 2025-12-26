@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold">👨‍🏫 Data Guru</h1>
            <p class="text-muted small mb-0">Manajemen akun dan profil pengajar di sekolah.</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            <i class="fas fa-plus me-2"></i> Tambah Guru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-lg mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 bg-white"
                               placeholder="Ketik Nama, NIP, atau NIK untuk mencari..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select bg-white">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Non-Aktif">Non-Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Pensiun">Pensiun</option>
                        <option value="Mutasi">Mutasi</option>
                        <option value="Pindah">Pindah</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-muted fst-italic" id="resultCount">Menampilkan seluruh data</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="teacherTable">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="px-4 py-3" style="width: 50px;">No</th>
                            <th class="py-3">Profil Guru</th>
                            <th class="py-3">NIP & Jabatan</th>
                            <th class="py-3">Kontak</th>
                            <th class="py-3">Status Pegawai</th>
                            <th class="py-3">Akun Login</th>
                            <th class="text-end px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($teachers as $index => $t)
                        <tr class="teacher-row">
                            <td class="px-4 text-center text-muted row-number">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                        {{ substr($t->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark search-name">{{ $t->nama_lengkap }}</div>
                                        <small class="text-muted">
                                            {{ $t->jenis_kelamin }} • {{ \Carbon\Carbon::parse($t->tanggal_lahir)->age }} Thn
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark search-nip">{{ $t->nip }}</div>
                                <small class="text-primary">{{ $t->jabatan }}</small>
                            </td>
                            <td>
                                <div class="text-dark small"><i class="far fa-envelope me-1 text-muted"></i> {{ $t->email }}</div>
                                <div class="text-muted small"><i class="fas fa-phone me-1 text-muted"></i> {{ $t->nomor_telepon }}</div>
                            </td>
                            <td>
                                @php
                                    // Logic warna status
                                    $badgeClass = 'bg-light text-dark'; // Default

                                    if($t->status == 'Aktif') $badgeClass = 'bg-success bg-opacity-10 text-success';
                                    elseif($t->status == 'Non-Aktif') $badgeClass = 'bg-secondary bg-opacity-10 text-secondary';
                                    elseif($t->status == 'Cuti') $badgeClass = 'bg-info bg-opacity-10 text-info';
                                    elseif($t->status == 'Pensiun') $badgeClass = 'bg-warning bg-opacity-10 text-warning';
                                    elseif($t->status == 'Mutasi') $badgeClass = 'bg-primary bg-opacity-10 text-primary';
                                    elseif($t->status == 'Pindah') $badgeClass = 'bg-dark text-white'; // HITAM SOLID (Kontras)
                                @endphp

                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 search-status">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td>
                                @if($t->user && $t->user->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-check me-1"></i> On</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger"><i class="fas fa-times me-1"></i> Off</span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light text-primary border-0 rounded-circle me-1"
                                            data-bs-toggle="modal" data-bs-target="#detailModal{{ $t->id }}" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-warning border-0 rounded-circle me-1"
                                            data-bs-toggle="modal" data-bs-target="#editTeacherModal{{ $t->id }}" title="Edit Lengkap">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    @if(Auth::id() != $t->user_id)
                                        <button class="btn btn-sm btn-light text-danger border-0 rounded-circle"
                                                onclick="deleteTeacher({{ $t->id }})" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <form id="delete-teacher-{{ $t->id }}"
                                              action="{{ route('guru.data.teachers.destroy', $t->id) }}"
                                              method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://img.freepik.com/free-vector/no-data-concept-illustration_114360-536.jpg" alt="No Data" style="width: 150px; opacity: 0.6;">
                                <p class="text-muted small mt-2">Data guru tidak ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse

                        <tr id="noResultsRow" style="display: none;">
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-search fa-2x mb-3 opacity-50"></i>
                                    <p>Tidak ditemukan guru dengan kata kunci tersebut.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($teachers as $t)
<div class="modal fade" id="detailModal{{ $t->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold text-primary"><i class="fas fa-id-card me-2"></i>Detail Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-4 text-center border-end">
                        <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ substr($t->nama_lengkap, 0, 1) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $t->nama_lengkap }}</h5>
                        <p class="text-muted small mb-2">{{ $t->nip }}</p>
                        <span class="badge bg-primary rounded-pill mb-3">{{ $t->jabatan }}</span>

                        <div class="text-start px-3 mt-3">
                            <p class="small text-muted mb-1"><i class="fas fa-calendar-alt me-2"></i>Bergabung</p>
                            <p class="fw-bold">{{ \Carbon\Carbon::parse($t->tanggal_masuk)->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3 border-bottom pb-2">Informasi Pribadi</h6>
                        <div class="row mb-3">
                            <div class="col-6 mb-3"><label class="small text-muted d-block">NIK</label><span class="fw-bold text-dark">{{ $t->nik }}</span></div>
                            <div class="col-6 mb-3"><label class="small text-muted d-block">Jenis Kelamin</label><span class="fw-bold text-dark">{{ $t->jenis_kelamin }}</span></div>
                            <div class="col-6 mb-3"><label class="small text-muted d-block">Tempat, Tgl Lahir</label><span class="fw-bold text-dark">{{ $t->tempat_lahir }}, {{ \Carbon\Carbon::parse($t->tanggal_lahir)->format('d/m/Y') }}</span></div>
                            <div class="col-6 mb-3"><label class="small text-muted d-block">Agama</label><span class="fw-bold text-dark">{{ $t->agama }}</span></div>
                        </div>

                        <h6 class="text-uppercase text-muted small fw-bold mb-3 border-bottom pb-2 pt-2">Kontak & Alamat</h6>
                        <div class="row">
                            <div class="col-6 mb-3"><label class="small text-muted d-block">Email</label><span class="fw-bold text-dark">{{ $t->email }}</span></div>
                            <div class="col-6 mb-3"><label class="small text-muted d-block">No. Telepon</label><span class="fw-bold text-dark">{{ $t->nomor_telepon }}</span></div>
                            <div class="col-12"><label class="small text-muted d-block">Alamat Lengkap</label><span class="fw-bold text-dark">{{ $t->alamat }}, {{ $t->kelurahan }}, {{ $t->kecamatan }}, {{ $t->kabupaten }}, {{ $t->provinsi }} - {{ $t->kode_pos }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@foreach($teachers as $t)
<div class="modal fade" id="editTeacherModal{{ $t->id }}" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form action="{{ route('guru.data.teachers.update', $t->id) }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header bg-warning bg-gradient text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Data Lengkap & Akun</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">

                <ul class="nav nav-tabs mb-3" id="editTab{{ $t->id }}" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="profile-tab{{ $t->id }}" data-bs-toggle="tab" data-bs-target="#profile{{ $t->id }}" type="button" role="tab">📂 Data Profil</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-danger" id="account-tab{{ $t->id }}" data-bs-toggle="tab" data-bs-target="#account{{ $t->id }}" type="button" role="tab">🔒 Akun Login & Password</button>
                    </li>
                </ul>

                <div class="tab-content" id="editTabContent{{ $t->id }}">
                    <div class="tab-pane fade show active" id="profile{{ $t->id }}" role="tabpanel">

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3">Identitas Utama</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">NIP <span class="text-danger">*</span></label>
                                        <input type="text" name="nip" class="form-control" value="{{ $t->nip }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_lengkap" class="form-control" value="{{ $t->nama_lengkap }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">NIK <span class="text-danger">*</span></label>
                                        <input type="number" name="nik" class="form-control" value="{{ $t->nik }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ $t->email }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3">Data Pribadi</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="jenis_kelamin" class="form-select" required>
                                            <option value="Laki-laki" {{ $t->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ $t->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" name="tempat_lahir" class="form-control" value="{{ $t->tempat_lahir }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ $t->tanggal_lahir }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Agama <span class="text-danger">*</span></label>
                                        <select name="agama" class="form-select" required>
                                            @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                                <option value="{{ $agama }}" {{ $t->agama == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">No. Telepon <span class="text-danger">*</span></label>
                                        <input type="text" name="nomor_telepon" class="form-control" value="{{ $t->nomor_telepon }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3">Alamat Domisili</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Alamat Jalan <span class="text-danger">*</span></label>
                                        <textarea name="alamat" class="form-control" rows="2" required>{{ $t->alamat }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Provinsi <span class="text-danger">*</span></label>
                                        <input type="text" name="provinsi" class="form-control" value="{{ $t->provinsi }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Kabupaten/Kota <span class="text-danger">*</span></label>
                                        <input type="text" name="kabupaten" class="form-control" value="{{ $t->kabupaten }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Kecamatan <span class="text-danger">*</span></label>
                                        <input type="text" name="kecamatan" class="form-control" value="{{ $t->kecamatan }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Kelurahan <span class="text-danger">*</span></label>
                                        <input type="text" name="kelurahan" class="form-control" value="{{ $t->kelurahan }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Dusun</label>
                                        <input type="text" name="dusun" class="form-control" value="{{ $t->dusun }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Kode Pos <span class="text-danger">*</span></label>
                                        <input type="number" name="kode_pos" class="form-control" value="{{ $t->kode_pos }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3">Kepegawaian</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Jabatan <span class="text-danger">*</span></label>
                                        <input type="text" name="jabatan" class="form-control" value="{{ $t->jabatan }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            <option value="Aktif" {{ $t->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Non-Aktif" {{ $t->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                            <option value="Cuti" {{ $t->status == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                            <option value="Pensiun" {{ $t->status == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                                            <option value="Mutasi" {{ $t->status == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                                            <option value="Pindah" {{ $t->status == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Tanggal Masuk <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_masuk" class="form-control" value="{{ $t->tanggal_masuk }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="account{{ $t->id }}" role="tabpanel">
                        <div class="card border-0 shadow-sm bg-white">
                            <div class="card-body">
                                <h6 class="fw-bold text-danger mb-3"><i class="fas fa-lock me-2"></i>Pengaturan Keamanan Akun</h6>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Status Akun Login</label>
                                        <select name="is_active" class="form-select">
                                            <option value="1" {{ $t->user && $t->user->is_active ? 'selected' : '' }}>✅ Aktif (Bisa Login)</option>
                                            <option value="0" {{ $t->user && !$t->user->is_active ? 'selected' : '' }}>❌ Non-Aktif (Dilarang Login)</option>
                                        </select>
                                        <div class="form-text small text-muted">Jika diset Non-Aktif, guru ini tidak akan bisa login ke sistem meskipun data profilnya masih ada.</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 small">
                                            <i class="fas fa-key me-1"></i> <b>Reset Password</b>
                                            <br>Masukkan password baru di bawah ini hanya jika Anda ingin mengubah password guru tersebut.
                                        </div>
                                        <label class="form-label small fw-bold">Password Baru (Opsional)</label>
                                        <input type="password" name="password" class="form-control" placeholder="Masukkan password baru...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="addTeacherModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form action="{{ route('guru.data.teachers.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header bg-primary bg-gradient text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Guru Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">📂 Identitas Utama</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip" class="form-control" required placeholder="Contoh: 1985xxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control" required placeholder="Nama lengkap dengan gelar">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NIK <span class="text-danger">*</span></label>
                                <input type="number" name="nik" class="form-control" required placeholder="Nomor Induk Kependudukan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email (Aktif) <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required placeholder="email@sekolah.sch.id">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">👤 Data Pribadi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Agama <span class="text-danger">*</span></label>
                                <select name="agama" class="form-select" required>
                                    <option value="" disabled selected>Pilih Agama...</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" name="nomor_telepon" class="form-control" required placeholder="08xxxxxxxx">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">🏠 Alamat Domisili</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Alamat Jalan <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="2" required placeholder="Nama Jalan, No Rumah, RT/RW"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" name="provinsi" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Kabupaten/Kota <span class="text-danger">*</span></label>
                                <input type="text" name="kabupaten" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" name="kecamatan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Kelurahan <span class="text-danger">*</span></label>
                                <input type="text" name="kelurahan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Dusun (Opsional)</label>
                                <input type="text" name="dusun" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Kode Pos <span class="text-danger">*</span></label>
                                <input type="number" name="kode_pos" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">💼 Kepegawaian</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" required placeholder="Contoh: Guru Mapel">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Non-Aktif">Non-Aktif</option>
                                    <option value="Pensiun">Pensiun</option>
                                    <option value="Mutasi">Mutasi</option>
                                    <option value="Pindah">Pindah</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_masuk" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info small mt-3 py-2 border-0 bg-opacity-10 bg-info text-info">
                    <i class="fas fa-info-circle me-1"></i> Akun login akan dibuat otomatis dengan username <b>NIP</b> dan password default: <b>password123</b>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Guru</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // --- FITUR LIVE SEARCH & FILTER (REAKTIF) ---
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('.teacher-row');
    const noResultsRow = document.getElementById('noResultsRow');
    const resultCount = document.getElementById('resultCount');

    function filterTable() {
        const query = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        let visibleCount = 0;

        tableRows.forEach(row => {
            // Ambil data dari kolom yang relevan
            const name = row.querySelector('.search-name').textContent.toLowerCase();
            const nip = row.querySelector('.search-nip').textContent.toLowerCase();
            const statusText = row.querySelector('.search-status').textContent.trim();

            // Cek apakah cocok dengan search query
            const matchesSearch = name.includes(query) || nip.includes(query);

            // Cek apakah cocok dengan filter status (jika ada filter)
            const matchesStatus = status === '' || statusText === status;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Tampilkan pesan jika tidak ada hasil
        if (visibleCount === 0) {
            noResultsRow.style.display = '';
        } else {
            noResultsRow.style.display = 'none';
        }

        // Update Text Jumlah
        resultCount.textContent = `Menampilkan ${visibleCount} data`;
    }

    // Pasang Event Listener
    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);


    // --- FITUR DELETE (SWEETALERT) ---
    function deleteTeacher(id) {
        Swal.fire({
            title: 'Hapus Data Guru?',
            text: "Data dan akun login akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-teacher-' + id).submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', showConfirmButton: false, timer: 1500 });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}' });
    @endif
</script>
@endpush
@endsection
