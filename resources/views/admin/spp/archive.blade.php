<x-app-layout>
    <div class="container-fluid px-4 mt-4">

        {{-- Notice / Alert Header --}}
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-white text-success p-3 rounded-circle me-3 shadow-sm">
                    <i class="fas fa-archive fa-2x"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Arsip Data Lunas</h5>
                    <small class="mb-0">Menampilkan riwayat pembayaran yang sudah selesai (Lunas).</small>
                </div>
            </div>
            <a href="{{ route('admin.spp.index') }}" class="btn btn-light rounded-pill shadow-sm fw-bold text-success">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Data Aktif
            </a>
        </div>

        {{-- Filter Sederhana --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.spp.archive') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Cari Nama / NISN..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="classroom_id" class="form-select bg-light border-0 rounded-pill" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="year" class="form-select bg-light border-0 rounded-pill" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary rounded-pill w-100 shadow-sm fw-bold">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.spp.archive') }}" class="btn btn-outline-secondary rounded-pill w-100 border-0 bg-light text-muted">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Arsip --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-success bg-opacity-10 text-success">
                            <tr>
                                <th class="px-4 py-3 border-0">Siswa</th>
                                <th class="border-0">Kelas</th>
                                <th class="border-0">Tagihan</th>
                                <th class="border-0">Nominal</th>
                                <th class="border-0">Tanggal Bayar</th>
                                <th class="text-end px-4 border-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archives as $p)
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user-check small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $p->student->nama_lengkap ?? 'Siswa Terhapus' }}</div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">{{ $p->student->nisn ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $p->student->classroom->nama_kelas ?? '-' }}</span></td>
                                <td><span class="fw-bold text-dark">{{ $p->month }} {{ $p->year }}</span></td>
                                <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">
                                        <i class="far fa-calendar-check me-1"></i>
                                        {{ \Carbon\Carbon::parse($p->paid_at)->translatedFormat('d M Y H:i') }}
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        {{-- Tombol Cetak Kwitansi (Coming Soon) --}}
                                        <button class="btn btn-sm btn-outline-dark rounded-circle shadow-sm" title="Cetak Kwitansi (Coming Soon)" disabled>
                                            <i class="fas fa-print"></i>
                                        </button>

                                        {{-- Tombol Hapus Arsip --}}
                                        <button type="button" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm hover-scale"
                                            onclick="confirmDeleteArchive({{ $p->id }})"
                                            title="Hapus Permanen">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <form id="delete-archive-{{ $p->id }}" action="{{ route('admin.spp.destroy', $p->id) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="fas fa-box-open fa-3x mb-3"></i>
                                        <p>Belum ada data arsip pembayaran lunas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $archives->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi Konfirmasi Hapus Arsip
        function confirmDeleteArchive(id) {
            Swal.fire({
                title: 'Hapus Arsip Permanen?',
                text: "PERINGATAN KERAS: Data ini adalah bukti pembayaran LUNAS (Uang Masuk). Menghapusnya akan menghilangkan jejak audit keuangan. Anda yakin?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah
                cancelButtonColor: '#3085d6', // Biru
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-archive-' + id).submit();
                }
            })
        }

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}" });
        @endif
    </script>
    @endpush

    <style>
        .hover-scale:hover { transform: scale(1.1); transition: transform 0.2s; }
        /* Fix Pagination Icon Size */
        nav svg { max-height: 20px; max-width: 20px; }
    </style>
</x-app-layout>
