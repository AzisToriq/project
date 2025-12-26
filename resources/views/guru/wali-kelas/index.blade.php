@extends('layouts.app')

@section('content')
<div class="py-4">

    {{-- HEADER & TOGGLE STATUS --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold">👨‍🏫 Monitoring Kelas {{ $classroom->nama_kelas }}</h1>
            <p class="text-muted small mb-0">Kelola status publikasi rapor dan cek hasil belajar siswa.</p>
        </div>

        {{-- Card Status Saklar --}}
        <div class="card border-0 shadow-sm {{ $classroom->is_rapor_published ? 'bg-success' : 'bg-secondary' }} text-white" style="min-width: 300px;">
            <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                <div>
                    <small class="d-block opacity-75 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">STATUS RAPOR</small>
                    <span class="fw-bold text-uppercase fs-6">
                        {{ $classroom->is_rapor_published ? '✅ DIBUKA (PUBLISHED)' : '🔒 TERTUTUP (LOCKED)' }}
                    </span>
                </div>

                <form id="formToggleRapor" action="{{ route('guru.rapor.toggle', $classroom->id) }}" method="POST" class="ms-3 ps-3 border-start border-white border-opacity-25">
                    @csrf
                    <button type="button"
                            onclick="confirmToggle({{ $classroom->is_rapor_published }})"
                            class="btn btn-sm {{ $classroom->is_rapor_published ? 'btn-danger' : 'btn-light text-success' }} rounded-pill fw-bold shadow-sm px-3 py-2">
                        @if($classroom->is_rapor_published)
                            <i class="fas fa-lock me-1"></i> Tutup
                        @else
                            <i class="fas fa-paper-plane me-1"></i> Rilis
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- TABEL SISWA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Daftar Siswa Perwalian</h6>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $students->count() }} Siswa</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="px-4 py-3 text-center" width="5%">No</th>
                            <th width="15%">NISN</th>
                            <th width="40%">Nama Lengkap</th>
                            <th class="text-center" width="10%">L/P</th>
                            <th class="text-end px-4" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $s)
                        <tr>
                            <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark font-monospace">{{ $s->nisn }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $s->nama_lengkap }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <i class="fas fa-id-card me-1"></i>NIS: {{ $s->nis ?? '-' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $s->jenis_kelamin == 'L' ? 'bg-info bg-opacity-10 text-info' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill">
                                    {{ $s->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                {{-- Tombol Preview (Mata) --}}
                                <button type="button"
                                        onclick="showPreview('{{ route('guru.rapor.show', $s->id) }}', '{{ $s->nama_lengkap }}')"
                                        class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm"
                                        title="Lihat Tampilan Rapor Siswa">
                                    <i class="fas fa-eye me-1"></i> Preview
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <img src="https://img.freepik.com/free-vector/no-data-concept-illustration_114360-536.jpg" width="150" class="mb-3 opacity-50" alt="No Data">
                                <p class="mb-0">Belum ada siswa terdaftar di kelas ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW (IFRAME) --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content h-100 border-0 rounded-4">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="fas fa-file-alt me-2"></i>Preview Rapor: <span id="previewStudentName" class="text-dark"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-secondary" style="height: 80vh;">
                {{-- Iframe untuk memuat halaman rapor siswa --}}
                <iframe id="raporFrame" src="about:blank" frameborder="0" style="width: 100%; height: 100%; display: block;"></iframe>
            </div>
            <div class="modal-footer bg-white py-2 shadow-lg">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <a id="btnDownload" href="#" target="_blank" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru / Cetak
                </a>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT PENDUKUNG (SweetAlert & Modal Logic) --}}
{{-- Pastikan Layout sudah load jQuery/Bootstrap JS, kalau belum tambahkan CDN di bawah ini --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Fungsi Konfirmasi Rilis (SweetAlert)
    function confirmToggle(isPublished) {
        let titleText = isPublished ? 'Tutup Akses Rapor?' : 'Rilis Rapor Sekarang?';

        let bodyText = isPublished
            ? 'Siswa tidak akan bisa melihat nilai rapor mereka lagi. Status kembali menjadi Tertutup.'
            : 'Pastikan seluruh nilai siswa sudah lengkap dan benar. Siswa akan dapat melihat nilai mereka di dashboard.';

        let confirmText = isPublished ? 'Ya, Tutup Akses!' : 'Ya, Rilis Rapor!';
        let btnColor = isPublished ? '#dc3545' : '#198754'; // Merah / Hijau

        Swal.fire({
            title: titleText,
            text: bodyText,
            icon: isPublished ? 'warning' : 'info',
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit Form
                document.getElementById('formToggleRapor').submit();
            }
        });
    }

    // 2. Fungsi Tampilkan Modal Preview
    function showPreview(url, name) {
        // Set Judul Modal
        document.getElementById('previewStudentName').innerText = name;

        // Reset Iframe dulu biar loading kelihatan (UX)
        let iframe = document.getElementById('raporFrame');
        iframe.src = "about:blank";

        // Set Source Iframe ke URL Rapor (Delay dikit biar smooth)
        setTimeout(() => {
            iframe.src = url;
        }, 100);

        // Set tombol download ke URL yg sama
        document.getElementById('btnDownload').href = url;

        // Tampilkan Modal
        var myModal = new bootstrap.Modal(document.getElementById('previewModal'));
        myModal.show();
    }
</script>
@endsection
