<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pembayaran Sekolah
        </h2>
    </x-slot>

    <div class="container-fluid px-4 mt-4">

        {{-- Info Siswa --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-white p-3 rounded-circle shadow-sm me-3 border">
                <i class="fas fa-user-graduate fa-2x text-primary"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-0">{{ $anak->nama_lengkap }}</h4>
                <p class="text-muted small mb-0">{{ $anak->nisn }} | Kelas {{ $anak->classroom->nama_kelas ?? '-' }}</p>
            </div>
        </div>

        {{-- Tabel Tagihan --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold text-dark"><i class="fas fa-file-invoice-dollar text-warning me-2"></i>Daftar Tagihan SPP</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Rincian Tagihan</th>
                                <th class="py-3">Nominal</th>
                                <th class="py-3">Status Pembayaran</th>
                                <th class="py-3 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tagihan_spp as $tagihan)
                            {{-- LOGIC CEK DEADLINE --}}
                            @php
                                $daysLeft = $tagihan->due_date ? now()->diffInDays($tagihan->due_date, false) : 999;
                                $isNearDeadline = ($daysLeft >= 0 && $daysLeft <= 3); // H-3
                                $isOverdue = ($daysLeft < 0); // Telat
                            @endphp

                            <tr class="@if($isOverdue && $tagihan->status == 'unpaid') bg-danger bg-opacity-10 @endif">
                                <td class="ps-4 align-middle">
                                    {{-- TAMPILKAN JUDUL TAGIHAN --}}
                                    @if($tagihan->title)
                                        <div class="fw-bold text-primary mb-1">{{ $tagihan->title }}</div>
                                        <div class="small text-muted">{{ $tagihan->month }} {{ $tagihan->year }}</div>
                                    @else
                                        <div class="fw-bold text-dark">{{ $tagihan->month }} {{ $tagihan->year }}</div>
                                    @endif

                                    {{-- TAMPILKAN JATUH TEMPO --}}
                                    @if($tagihan->due_date)
                                        <div class="small {{ $isOverdue && $tagihan->status == 'unpaid' ? 'text-danger fw-bold' : 'text-muted' }}">
                                            <i class="far fa-calendar-times me-1"></i>
                                            Jatuh Tempo: {{ $tagihan->due_date->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</div>

                                    {{-- SMART ALERT: HAMPIR JATUH TEMPO & BELUM ADA DISPENSASI --}}
                                    @if($tagihan->status == 'unpaid' && $isNearDeadline && !$tagihan->dispensation_status)
                                        <div class="alert alert-warning border-0 p-2 mt-2 mb-0 d-flex align-items-center shadow-sm" role="alert">
                                            <div class="me-2 text-warning" style="font-size: 1.2rem;"><i class="fas fa-exclamation-circle"></i></div>
                                            <div class="lh-1 flex-grow-1">
                                                <small class="fw-bold d-block text-dark" style="font-size: 0.75rem;">Segera Bayar!</small>
                                                <small class="text-muted" style="font-size: 0.65rem;">Atau ajukan dispensasi.</small>
                                            </div>
                                            <button class="btn btn-sm btn-light text-warning fw-bold ms-2 rounded-pill px-2 shadow-sm border"
                                                    style="font-size: 0.7rem;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#dispensationModal{{ $tagihan->id }}">
                                                Ajukan
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{-- LOGIC STATUS PEMBAYARAN --}}
                                    @if($tagihan->status == 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Lunas</span>
                                        <small class="text-muted ms-1">{{ $tagihan->paid_at ? \Carbon\Carbon::parse($tagihan->paid_at)->format('d M Y') : '' }}</small>
                                    @elseif($tagihan->status == 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Sedang Diverifikasi</span>
                                    @else
                                        {{-- JIKA BELUM LUNAS, CEK DISPENSASI --}}
                                        @if($tagihan->dispensation_status == 'pending')
                                            <span class="badge bg-warning bg-opacity-25 text-warning-emphasis rounded-pill px-3 mb-1 border border-warning" title="Menunggu persetujuan Admin">
                                                <i class="fas fa-hourglass-half me-1"></i> Menunggu Persetujuan
                                            </span>
                                            <div class="text-muted small mt-1">Janji: {{ \Carbon\Carbon::parse($tagihan->dispensation_date)->format('d M') }}</div>

                                        @elseif($tagihan->dispensation_status == 'approved')
                                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 mb-1 border border-info" title="{{ $tagihan->dispensation_note }}">
                                                <i class="fas fa-hand-holding-heart me-1"></i> Disetujui: {{ \Carbon\Carbon::parse($tagihan->dispensation_date)->format('d M') }}
                                            </span>

                                        @elseif($tagihan->dispensation_status == 'rejected')
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 mb-1 border border-danger">
                                                <i class="fas fa-times-circle me-1"></i> Pengajuan Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Belum Lunas</span>
                                        @endif

                                        {{-- TAMPILKAN CATATAN JIKA ADA --}}
                                        @if($tagihan->note)
                                            <div class="text-danger small mt-1 fw-bold"><i class="fas fa-info-circle me-1"></i> {{ $tagihan->note }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    {{-- LOGIC TOMBOL AKSI --}}
                                    @if($tagihan->status == 'unpaid')

                                        <div class="d-flex justify-content-end gap-2">

                                            {{-- TOMBOL DISPENSASI (Jika belum pernah / ditolak) --}}
                                            @if(!$tagihan->dispensation_status || $tagihan->dispensation_status == 'rejected')
                                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#dispensationModal{{ $tagihan->id }}" title="Ajukan Penundaan">
                                                    <i class="fas fa-clock"></i> Nanti
                                                </button>
                                            @endif

                                            {{-- TOMBOL BAYAR --}}
                                            <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#payModal{{ $tagihan->id }}">
                                                <i class="fas fa-upload me-1"></i> Bayar
                                            </button>
                                        </div>

                                        {{-- MODAL DISPENSASI --}}
                                        <div class="modal fade text-start" id="dispensationModal{{ $tagihan->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-md modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h6 class="modal-title fw-bold text-secondary">Ajukan Dispensasi</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('ortu.tagihan.dispensasi', $tagihan->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body p-4">
                                                            <div class="alert alert-warning small border-0 bg-warning bg-opacity-10 mb-3 text-warning-emphasis">
                                                                <i class="fas fa-info-circle me-1"></i> Pengajuan ini harus disetujui oleh Admin Sekolah.
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Kapan rencana akan membayar?</label>
                                                                <input type="date" name="date" class="form-control rounded-3 form-control-lg" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Alasan Penundaan</label>
                                                                <textarea name="note" class="form-control rounded-3" rows="3" placeholder="Contoh: Menunggu gaji tanggal 25..." required></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Kirim Pengajuan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- MODAL UPLOAD BAYAR --}}
                                        <div class="modal fade text-start" id="payModal{{ $tagihan->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Konfirmasi Pembayaran</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('ortu.tagihan.pay', $tagihan->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body p-4">
                                                            <div class="alert alert-info small border-0 bg-info bg-opacity-10 mb-3">
                                                                Silakan transfer <b>Rp {{ number_format($tagihan->amount) }}</b> ke Rekening Sekolah:<br>
                                                                <b>BRI: 1234-5678-9000 (Siskol Indonesia)</b><br>
                                                                Lalu upload bukti transfer di bawah ini.
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Upload Bukti Transfer</label>
                                                                <input type="file" name="proof_file" class="form-control rounded-3" accept="image/*,.pdf" required>
                                                                <div class="form-text small">Format: JPG, PNG, PDF. Maks 2MB.</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Bukti</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($tagihan->status == 'pending')
                                        <button class="btn btn-light btn-sm text-muted rounded-pill px-3" disabled>
                                            <i class="fas fa-clock me-1"></i> Menunggu Admin
                                        </button>
                                    @else
                                        {{-- TOMBOL LUNAS (FIXED COLOR: Hijau Teks Putih) --}}
                                        <button class="btn btn-success btn-sm text-white rounded-pill px-3 shadow-sm" style="cursor: default; opacity: 1;" disabled>
                                            <i class="fas fa-check-circle me-1"></i> Lunas
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-smile fa-3x mb-3 opacity-25"></i><br>
                                    Hore! Belum ada tagihan SPP.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}" });
        @endif
        @if(session('warning'))
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: "{{ session('warning') }}" });
        @endif
    </script>
    @endpush
</x-app-layout>
