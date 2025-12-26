<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Wali Murid
        </h2>
    </x-slot>

    <div class="container-fluid px-4 mt-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 text-gray-800 font-weight-bold">👨‍👩‍👧‍👦 Data Wali Murid</h1>
                <p class="text-muted small mb-0">Pilih kelas untuk melihat data orang tua siswa.</p>
            </div>

            {{-- Tombol Tambah Akun (Global) --}}
            <button class="btn btn-primary rounded-pill shadow-sm px-4" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i> Buat Akun Baru
            </button>
        </div>

        {{-- ALERT: LOGIC DETEKSI AKUN MENGGANTUNG --}}
        @if(isset($unlinked_parents) && $unlinked_parents > 0)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center bg-warning bg-opacity-10 text-dark">
            <div class="me-3 text-warning">
                <i class="fas fa-exclamation-triangle fa-2x"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1">Perhatian</h6>
                <small class="d-block text-muted" style="line-height: 1.5;">
                    Ada <b class="text-danger">{{ $unlinked_parents }}</b> akun orang tua yang sudah dibuat tapi belum terhubung ke siswa manapun.<br>
                    Silakan masuk ke menu <b><a href="{{ route('guru.data.students.index') }}" class="fw-bold text-dark text-decoration-underline">Data Siswa</a></b>, edit siswa yang bersangkutan, lalu pilih akun orang tua tersebut.
                </small>
            </div>
        </div>
        @endif

        {{-- Grid Kelas --}}
        <div class="row g-4">
            @foreach($classrooms as $kelas)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('guru.data.parents.show_class', $kelas->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-up rounded-4 overflow-hidden position-relative">
                        <div class="card-body p-4 d-flex flex-column position-relative z-1">

                            {{-- Header Kartu --}}
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                    <i class="fas fa-user-friends fa-2x"></i>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    {{ $kelas->students_count }} Siswa
                                </span>
                            </div>

                            {{-- Footer Kartu --}}
                            <div class="mt-auto">
                                <h5 class="fw-bold text-dark mb-1">{{ $kelas->nama_kelas }}</h5>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>
                                    <span class="text-truncate" style="max-width: 200px;">
                                        Wali Kelas: {{ $kelas->waliKelas->nama_lengkap ?? '-' }}
                                    </span>
                                </div>
                            </div>

                        </div>

                        {{-- Hiasan Background --}}
                        <div class="position-absolute bottom-0 end-0 opacity-10 p-3">
                            <i class="fas fa-users fa-4x text-secondary"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL TAMBAH AKUN (GLOBAL) --}}
    <div class="modal fade" id="parentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form action="{{ route('guru.data.parents.store') }}" method="POST" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Tambah Akun Orang Tua</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info-emphasis small mb-4">
                        <div class="d-flex">
                            <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                            <div>
                                <b>PENTING:</b><br>
                                Form ini hanya untuk membuat <b>Akun Login</b>.<br>
                                Agar akun ini aktif, Anda harus menautkannya lewat menu <b>Data Siswa</b> setelah akun dibuat.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username Login</label>
                        <input type="text" name="username" class="form-control rounded-3" placeholder="Contoh: budi123" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Aktif</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="email@contoh.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openAddModal() {
            new bootstrap.Modal(document.getElementById('parentModal')).show();
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
    @endpush

    <style>
        .hover-up { transition: transform 0.2s; }
        .hover-up:hover { transform: translateY(-5px); }
    </style>
</x-app-layout>
