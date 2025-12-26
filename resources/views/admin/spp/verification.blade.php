<x-app-layout>
    <div class="container-fluid px-4 mt-4">

        {{-- Header & Navigasi --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('admin.spp.index') }}" class="btn btn-link text-secondary p-0 mb-2 text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Data SPP
                </a>
                <h1 class="h3 text-gray-800 font-weight-bold">
                    <i class="fas fa-check-circle text-warning me-2"></i>Antrean Verifikasi
                </h1>
                <p class="text-muted small mb-0">
                    Ada <b>{{ $pendingPayments->count() }}</b> pembayaran menunggu validasi Anda.
                </p>
            </div>
        </div>

        @if($pendingPayments->count() > 0)
            <div class="row g-4">
                @foreach($pendingPayments as $p)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 rounded-4 shadow-sm h-100 hover-up position-relative overflow-hidden">

                        {{-- Badge Tanggal Upload --}}
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-white text-muted border shadow-sm">
                                <i class="far fa-clock me-1"></i> {{ $p->updated_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="card-body p-4 text-center d-flex flex-column">
                            {{-- Foto Profil Siswa (Inisial) --}}
                            <div class="mx-auto mb-3">
                                <div class="avatar-circle bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 60px; height: 60px;">
                                    {{ substr($p->student->nama_lengkap, 0, 1) }}
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $p->student->nama_lengkap }}</h6>
                            <p class="text-muted small mb-3">{{ $p->student->classroom->nama_kelas ?? '-' }}</p>

                            <div class="bg-light rounded-3 p-2 mb-3 border">
                                <small class="d-block text-muted text-uppercase" style="font-size: 10px;">Tagihan</small>
                                <span class="fw-bold text-dark">{{ $p->month }} {{ $p->year }}</span>
                                <div class="text-primary fw-bold mt-1">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                            </div>

                            <div class="mt-auto">
                                <button class="btn btn-warning text-white rounded-pill w-100 shadow-sm fw-bold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verifyModal{{ $p->id }}">
                                    <i class="fas fa-search me-1"></i> Cek Bukti
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- MODAL VERIFIKASI (PER ITEM) --}}
                {{-- ========================================== --}}
                <div class="modal fade" id="verifyModal{{ $p->id }}" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Validasi Pembayaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 text-center">

                                {{-- Preview Gambar --}}
                                @if($p->proof_file)
                                    <div class="bg-dark rounded-3 p-1 mb-3 position-relative overflow-hidden" style="min-height: 200px;">
                                        <img src="{{ asset('storage/' . $p->proof_file) }}"
                                             class="img-fluid rounded"
                                             style="max-height: 400px; width: 100%; object-fit: contain;">

                                        <a href="{{ asset('storage/' . $p->proof_file) }}" target="_blank" class="btn btn-sm btn-light position-absolute bottom-0 end-0 m-3 shadow rounded-pill opacity-90">
                                            <i class="fas fa-expand me-1"></i> Zoom
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-danger">File bukti rusak/tidak ditemukan.</div>
                                @endif

                                <div class="text-start mb-4">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small class="text-muted">Siswa</small>
                                            <div class="fw-bold">{{ $p->student->nama_lengkap }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Nominal</small>
                                            <div class="fw-bold text-primary">Rp {{ number_format($p->amount) }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Aksi --}}
                                <div class="d-grid gap-2">
                                    {{-- TERIMA --}}
                                    <form action="{{ route('admin.spp.verify', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 rounded-pill py-2 shadow-sm">
                                            <i class="fas fa-check-circle me-2"></i> Terima & Lunas
                                        </button>
                                    </form>

                                    {{-- TOLAK (Collapse) --}}
                                    <button class="btn btn-outline-danger w-100 rounded-pill py-2" type="button" data-bs-toggle="collapse" data-bs-target="#rejectReason{{ $p->id }}">
                                        <i class="fas fa-times-circle me-2"></i> Tolak / Minta Revisi
                                    </button>

                                    <div class="collapse mt-2" id="rejectReason{{ $p->id }}">
                                        <form action="{{ route('admin.spp.reject', $p->id) }}" method="POST" class="card card-body bg-light border-0 p-3 text-start">
                                            @csrf
                                            <label class="small fw-bold text-muted mb-1">Alasan Penolakan:</label>
                                            <textarea name="note" class="form-control form-control-sm mb-2" rows="2" placeholder="Contoh: Bukti buram, Nominal tidak sesuai..." required></textarea>
                                            <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill">
                                                Konfirmasi Penolakan
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            {{-- State Kosong --}}
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex p-5 mb-4 shadow-sm">
                    <i class="fas fa-clipboard-check fa-4x text-success opacity-50"></i>
                </div>
                <h4 class="fw-bold text-gray-800">Semua Beres!</h4>
                <p class="text-muted">Tidak ada pembayaran yang perlu diverifikasi saat ini.</p>
                <a href="{{ route('admin.spp.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                    Kembali ke Data SPP
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 1500, showConfirmButton: false });
        @endif
        @if(session('warning'))
            Swal.fire({ icon: 'warning', title: 'Ditolak', text: "{{ session('warning') }}" });
        @endif
    </script>
    @endpush

    <style>
        .hover-up { transition: transform 0.2s; }
        .hover-up:hover { transform: translateY(-5px); }
    </style>
</x-app-layout>
