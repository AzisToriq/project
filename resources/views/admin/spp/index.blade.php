<x-app-layout>
    <div class="container-fluid px-4 mt-4">

        {{-- Header & Tombol Action --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Manajemen SPP</h2>
                <p class="text-muted mb-0">Kelola tagihan, validasi pembayaran, dan persetujuan dispensasi.</p>
            </div>
            <div class="d-flex gap-2">
                {{-- Shortcut ke Arsip (Khusus Lunas) --}}
                <a href="{{ route('admin.spp.archive') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-archive me-2"></i> Arsip Lunas
                </a>

                {{-- Shortcut Verifikasi (Badge Merah untuk Bukti Bayar) --}}
                <a href="{{ route('admin.spp.verification') }}" class="btn btn-warning text-white rounded-pill px-3 shadow-sm position-relative">
                    <i class="fas fa-bell"></i>
                    @php $pendingCount = \App\Models\SppPayment::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                {{-- Buat Tagihan --}}
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="fas fa-plus-circle me-2"></i> Buat Tagihan
                </button>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-lg-9">
                        <form action="{{ route('admin.spp.index') }}" method="GET" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold mb-1">Kelas</label>
                                <select name="classroom_id" class="form-select border-light bg-light rounded-3 shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    @foreach($classrooms as $kelas)
                                        <option value="{{ $kelas->id }}" {{ request('classroom_id') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold mb-1">Bulan</label>
                                <select name="month" class="form-select border-light bg-light rounded-3 shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Bulan</option>
                                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                                        <option value="{{ $bulan }}" {{ request('month') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold mb-1">Status</label>
                                <select name="status" class="form-select border-light bg-light rounded-3 shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Verifikasi</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold mb-1">Publikasi</label>
                                <select name="publish_status" class="form-select border-light bg-light rounded-3 shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    <option value="draft" {{ request('publish_status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ request('publish_status') == 'published' ? 'selected' : '' }}>Terbit</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    {{-- Tombol Aksi Massal --}}
                    <div class="col-lg-3 d-flex align-items-end justify-content-end gap-2">
                        @if(request('classroom_id') || request('month') || request('year'))
                            <form id="publish-all-form" action="{{ route('admin.spp.publish_all') }}" method="POST">
                                @csrf
                                @foreach(request()->all() as $key => $value) <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endforeach
                                <button type="button" onclick="confirmPublishAll()" class="btn btn-success text-white rounded-3 shadow-sm" title="Terbitkan Draft">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            </form>

                            <form id="delete-all-form" action="{{ route('admin.spp.delete_all') }}" method="POST">
                                @csrf @method('DELETE')
                                @foreach(request()->all() as $key => $value) <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endforeach
                                <button type="button" onclick="confirmDeleteAll()" class="btn btn-danger text-white rounded-3 shadow-sm" title="Hapus Unpaid">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>

                            <a href="{{ route('admin.spp.index') }}" class="btn btn-light rounded-3 text-muted border shadow-sm" title="Reset Filter">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 small text-uppercase text-secondary">Siswa (Urut Absen)</th>
                                <th class="py-3 small text-uppercase text-secondary">Tagihan</th>
                                <th class="py-3 small text-uppercase text-secondary">Nominal</th>
                                <th class="py-3 small text-uppercase text-secondary">Publikasi</th>
                                <th class="py-3 small text-uppercase text-secondary">Status</th>
                                <th class="py-3 small text-uppercase text-secondary text-end px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $prevStudentId = null; @endphp

                            @forelse($payments as $spp)
                                @php
                                    $isSameStudent = ($prevStudentId == $spp->student_id);
                                    $borderClass = $isSameStudent ? 'border-top-0' : '';

                                    // Row coloring logic
                                    $rowClass = '';
                                    if($spp->status == 'paid') {
                                        $rowClass = 'bg-success bg-opacity-10';
                                    } elseif($spp->dispensation_status == 'pending') {
                                        // Highlight kuning jika ada request dispensasi
                                        $rowClass = 'bg-warning bg-opacity-10';
                                    }
                                @endphp

                                <tr class="{{ $rowClass }}">
                                    {{-- KOLOM SISWA --}}
                                    <td class="px-4 {{ $borderClass }}" style="vertical-align: top;">
                                        @if(!$isSameStudent)
                                            <div class="d-flex align-items-center py-2">
                                                <div class="bg-white text-primary border rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 35px; height: 35px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $spp->student->nama_lengkap ?? 'Siswa Terhapus' }}</h6>
                                                    <small class="text-muted">{{ $spp->student->classroom->nama_kelas ?? '-' }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="{{ $borderClass }} align-middle">
                                        {{-- MENAMPILKAN JUDUL TAGIHAN --}}
                                        @if($spp->title)
                                            <div class="fw-bold text-primary mb-1">{{ $spp->title }}</div>
                                            <div class="small text-muted">{{ $spp->month }} {{ $spp->year }}</div>
                                        @else
                                            <span class="fw-bold text-dark">{{ $spp->month }} {{ $spp->year }}</span>
                                        @endif

                                        @if($spp->due_date)
                                            <div class="small text-muted" style="font-size: 0.75rem;">
                                                Tempo: {{ $spp->due_date->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="{{ $borderClass }} align-middle">
                                        @if($spp->status == 'unpaid')
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $spp->id }}" class="text-decoration-none fw-bold text-dark border-bottom border-secondary border-opacity-25">
                                                Rp {{ number_format($spp->amount, 0, ',', '.') }}
                                            </a>
                                        @else
                                            <span class="fw-bold {{ $spp->status == 'paid' ? 'text-success' : 'text-dark' }}">
                                                Rp {{ number_format($spp->amount, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="{{ $borderClass }} align-middle">
                                        <form action="{{ route('admin.spp.toggle', $spp->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $spp->is_published ? 'btn-success bg-gradient' : 'btn-secondary bg-opacity-50' }} rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem; min-width: 80px;">
                                                {{ $spp->is_published ? 'TERBIT' : 'DRAFT' }}
                                            </button>
                                        </form>
                                    </td>

                                    {{-- KOLOM STATUS (LOGIC DISPENSASI) --}}
                                    <td class="{{ $borderClass }} align-middle">
                                        @if($spp->status == 'paid')
                                            <span class="badge bg-success text-white px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-check me-1"></i> Lunas</span>
                                        @elseif($spp->status == 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm blink-soft"><i class="fas fa-clock me-1"></i> Verifikasi</span>
                                        @else
                                            {{-- CEK STATUS DISPENSASI --}}
                                            @if($spp->dispensation_status == 'pending')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill border border-warning blink-soft" title="Ada pengajuan baru!">
                                                    <i class="fas fa-hand-paper me-1"></i> Request Dispensasi
                                                </span>
                                            @elseif($spp->dispensation_status == 'approved')
                                                <span class="badge bg-info text-dark px-3 py-2 rounded-pill border border-info" title="{{ $spp->dispensation_note }}">
                                                    <i class="fas fa-calendar-check me-1"></i> Janji: {{ \Carbon\Carbon::parse($spp->dispensation_date)->format('d M') }}
                                                </span>
                                            @elseif($spp->dispensation_status == 'rejected')
                                                <span class="badge bg-danger text-white px-3 py-2 rounded-pill border border-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Ditolak
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill border border-danger">Belum Bayar</span>
                                            @endif
                                        @endif
                                    </td>

                                    <td class="text-end px-4 {{ $borderClass }} align-middle">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($spp->status == 'paid')
                                                {{-- OPSI BATALKAN --}}
                                                <form id="cancel-form-{{ $spp->id }}" action="{{ route('admin.spp.cancel_payment', $spp->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" title="Batalkan Pembayaran" onclick="confirmCancel({{ $spp->id }})">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>

                                            @elseif($spp->status == 'pending')
                                                {{-- OPSI CEK BUKTI --}}
                                                <button class="btn btn-sm btn-warning text-white rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $spp->id }}">
                                                    <i class="fas fa-search me-1"></i> Cek
                                                </button>

                                            @else
                                                {{-- STATUS UNPAID (BANYAK OPSI) --}}

                                                {{-- 1. Bayar Tunai (Manual) --}}
                                                <form id="manual-pay-form-{{ $spp->id }}" action="{{ route('admin.spp.pay_manual', $spp->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-success rounded-circle shadow-sm me-1" title="Bayar Manual (Tunai)" onclick="confirmManualPay({{ $spp->id }})">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </button>
                                                </form>

                                                {{-- 2. Tombol Dispensasi --}}
                                                @if($spp->dispensation_status == 'pending')
                                                    <button class="btn btn-sm btn-warning text-dark rounded-circle shadow-sm blink-soft" data-bs-toggle="modal" data-bs-target="#approvalModal{{ $spp->id }}" title="Cek Pengajuan Dispensasi">
                                                        <i class="fas fa-gavel"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline-info rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#dispensationModal{{ $spp->id }}" title="Input Dispensasi Manual">
                                                        <i class="fas fa-hand-holding-heart"></i>
                                                    </button>
                                                @endif

                                                {{-- 3. Hapus --}}
                                                <button type="button" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm hover-scale"
                                                    onclick="confirmDelete({{ $spp->id }}, false)" title="Hapus Data">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $spp->id }}" action="{{ route('admin.spp.destroy', $spp->id) }}" method="POST" style="display: none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @php $prevStudentId = $spp->student_id; @endphp

                                {{-- MODAL 1: MANUAL DISPENSASI (UKURAN LEGA) --}}
                                <div class="modal fade" id="dispensationModal{{ $spp->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <form action="{{ route('admin.spp.dispensation', $spp->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-0 pb-0">
                                                    <h6 class="modal-title fw-bold text-info"><i class="fas fa-edit me-1"></i> Manual Dispensasi</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="alert alert-info border-0 bg-info bg-opacity-10 small mb-3">
                                                        <i class="fas fa-info-circle me-1"></i> Admin mencatat janji bayar ini secara manual (Otomatis Disetujui).
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small fw-bold mb-1">Janji Bayar Tanggal</label>
                                                        <input type="date" name="date" class="form-control rounded-3" value="{{ $spp->dispensation_date ? $spp->dispensation_date->format('Y-m-d') : '' }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small fw-bold mb-1">Catatan</label>
                                                        <textarea name="note" class="form-control rounded-3" rows="3" placeholder="Contoh: Orang tua datang ke sekolah minta mundur tanggal..." required>{{ $spp->dispensation_note }}</textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-info text-white rounded-pill w-100 fw-bold">Simpan & Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL 2: APPROVAL DISPENSASI (Cek Pengajuan Ortu) --}}
                                @if($spp->dispensation_status == 'pending')
                                <div class="modal fade" id="approvalModal{{ $spp->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow-lg">
                                            <div class="modal-header border-0 pb-0">
                                                <h6 class="modal-title fw-bold text-dark"><i class="fas fa-bell me-2 text-warning"></i>Pengajuan Dispensasi</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-center">
                                                <div class="bg-warning bg-opacity-10 p-3 rounded-3 mb-3 border border-warning">
                                                    <h6 class="fw-bold text-dark mb-1">Janji Bayar:</h6>
                                                    <h3 class="text-primary fw-bold mb-2">{{ \Carbon\Carbon::parse($spp->dispensation_date)->format('d F Y') }}</h3>
                                                    <p class="mb-0 small text-muted fst-italic">"{{ $spp->dispensation_note }}"</p>
                                                </div>
                                                <p class="small text-muted">Apakah Anda menyetujui penundaan pembayaran ini?</p>

                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <form action="{{ route('admin.spp.dispensation.reject', $spp->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-bold">Tolak</button>
                                                        </form>
                                                    </div>
                                                    <div class="col-6">
                                                        <form action="{{ route('admin.spp.dispensation.approve', $spp->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold text-white">Setujui</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- MODAL 3: EDIT NOMINAL --}}
                                <div class="modal fade" id="editModal{{ $spp->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <form action="{{ route('admin.spp.update', $spp->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body p-4 text-center">
                                                    <h6 class="fw-bold mb-3">Edit Nominal</h6>
                                                    <div class="form-floating mb-3">
                                                        <input type="number" name="amount" class="form-control rounded-3" value="{{ $spp->amount }}">
                                                        <label>Nominal (Rp)</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary rounded-pill w-100">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL 4: VERIFIKASI BUKTI BAYAR --}}
                                @if($spp->status == 'pending')
                                <div class="modal fade" id="verifyModal{{ $spp->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow-lg">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Validasi Pembayaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center p-4">
                                                @if($spp->proof_file)
                                                    <div class="mb-3 bg-light rounded-3 p-2 border">
                                                        <img src="{{ asset('storage/'.$spp->proof_file) }}" class="img-fluid rounded shadow-sm" style="max-height: 350px;">
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/'.$spp->proof_file) }}" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3">
                                                                <i class="fas fa-expand me-1"></i> Zoom
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-danger">File bukti tidak ditemukan.</div>
                                                @endif

                                                <div class="d-grid gap-2">
                                                    <form action="{{ route('admin.spp.verify', $spp->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success w-100 rounded-pill py-2 shadow-sm fw-bold">
                                                            <i class="fas fa-check-circle me-1"></i> Terima Pembayaran
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-outline-danger w-100 rounded-pill py-2" type="button" data-bs-toggle="collapse" data-bs-target="#rejectForm{{ $spp->id }}">
                                                        <i class="fas fa-times-circle me-1"></i> Tolak Pembayaran
                                                    </button>
                                                    <div class="collapse mt-2" id="rejectForm{{ $spp->id }}">
                                                        <form action="{{ route('admin.spp.reject', $spp->id) }}" method="POST" class="card card-body border-0 bg-light p-3 text-start">
                                                            @csrf
                                                            <label class="small text-muted fw-bold mb-1">Alasan Penolakan:</label>
                                                            <textarea name="note" class="form-control form-control-sm mb-2" rows="2" required></textarea>
                                                            <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill">Konfirmasi Tolak</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <p>Data tidak ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $payments->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- MODAL GENERATE BARU (TABBED: MASSAL & SATUAN) --}}
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Buat Tagihan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Nav Tabs --}}
                    <ul class="nav nav-tabs nav-fill mb-3" id="generateTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" id="massal-tab" data-bs-toggle="tab" data-bs-target="#massal" type="button">
                                <i class="fas fa-users me-1"></i> Massal (Per Kelas)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="satuan-tab" data-bs-toggle="tab" data-bs-target="#satuan" type="button">
                                <i class="fas fa-user me-1"></i> Satuan (Per Siswa)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="generateTabContent">
                        {{-- TAB 1: FORM MASSAL --}}
                        <div class="tab-pane fade show active" id="massal" role="tabpanel">
                            <form action="{{ route('admin.spp.store') }}" method="POST">
                                @csrf
                                <div class="alert alert-info small border-0 bg-info bg-opacity-10 mb-3">
                                    Tagihan akan dibuat untuk <b>SEMUA SISWA</b> di kelas yang dipilih.
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Pilih Kelas</label>
                                    <select name="classroom_id" class="form-select bg-light border-0" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classrooms as $c)<option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>@endforeach
                                    </select>
                                </div>

                                {{-- FORM FIELD COMMON (INCLUDE) --}}
                                @include('admin.spp.partials.form-fields')

                                <button type="submit" class="btn btn-primary rounded-pill w-100 mt-3">Generate Massal</button>
                            </form>
                        </div>

                        {{-- TAB 2: FORM SATUAN --}}
                        <div class="tab-pane fade" id="satuan" role="tabpanel">
                            <form action="{{ route('admin.spp.store_individual') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Pilih Kelas</label>
                                    <select id="class_selector" class="form-select bg-light border-0" required onchange="loadStudents(this.value)">
                                        <option value="">-- Pilih Kelas Dulu --</option>
                                        @foreach($classrooms as $c)<option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Pilih Siswa</label>
                                    <select name="student_id" id="student_selector" class="form-select bg-light border-0" required disabled>
                                        <option value="">-- Menunggu Pilihan Kelas --</option>
                                    </select>
                                </div>

                                {{-- FORM FIELD COMMON (INCLUDE) --}}
                                @include('admin.spp.partials.form-fields')

                                <button type="submit" class="btn btn-success rounded-pill w-100 mt-3">Buat Tagihan Spesifik</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // AJAX: Load Siswa saat Kelas dipilih (Mode Satuan)
        function loadStudents(classId) {
            let studentSelect = document.getElementById('student_selector');
            studentSelect.innerHTML = '<option>Loading...</option>';
            studentSelect.disabled = true;

            if(!classId) return;

            fetch(`/admin/spp/get-students/${classId}`)
                .then(response => response.json())
                .then(data => {
                    studentSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';
                    data.forEach(student => {
                        studentSelect.innerHTML += `<option value="${student.id}">${student.nama_lengkap}</option>`;
                    });
                    studentSelect.disabled = false;
                });
        }

        // Konfirmasi Bayar Manual
        function confirmManualPay(id) {
            Swal.fire({
                title: 'Terima Pembayaran Tunai?',
                text: "Status akan berubah menjadi LUNAS. Pastikan uang sudah diterima.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Ya, Terima Tunai!'
            }).then((result) => { if (result.isConfirmed) document.getElementById('manual-pay-form-' + id).submit(); })
        }

        // Konfirmasi Batalkan Pembayaran
        function confirmCancel(id) {
            Swal.fire({
                title: 'Batalkan Pembayaran?',
                text: "Status akan kembali menjadi BELUM LUNAS (Unpaid).",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Batalkan!'
            }).then((result) => { if (result.isConfirmed) document.getElementById('cancel-form-' + id).submit(); })
        }

        // Fungsi Delete & Publish
        function confirmDelete(id) {
            Swal.fire({ title: 'Hapus Tagihan?', text: "Data hilang permanen!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!' })
            .then((result) => { if (result.isConfirmed) document.getElementById('delete-form-' + id).submit(); })
        }
        function confirmPublishAll() {
            Swal.fire({ title: 'Terbitkan Semua?', text: "Semua draft akan diterbitkan.", icon: 'question', showCancelButton: true, confirmButtonColor: '#198754', confirmButtonText: 'Ya, Terbitkan!' })
            .then((result) => { if (result.isConfirmed) document.getElementById('publish-all-form').submit(); })
        }
        function confirmDeleteAll() {
            Swal.fire({ title: 'Hapus Semua?', text: "Hanya yang Unpaid dihapus.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!' })
            .then((result) => { if (result.isConfirmed) document.getElementById('delete-all-form').submit(); })
        }

        @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false }); @endif
        @if(session('warning')) Swal.fire({ icon: 'warning', title: 'Perhatian', text: "{{ session('warning') }}" }); @endif
        @if(session('error')) Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}" }); @endif
    </script>
    <style>
        .blink-soft { animation: blinker 1.5s linear infinite; }
        @keyframes blinker { 50% { opacity: 0.6; } }
        .hover-scale:hover { transform: scale(1.1); transition: transform 0.2s; }
        nav svg { max-height: 20px; max-width: 20px; }
    </style>
</x-app-layout>
