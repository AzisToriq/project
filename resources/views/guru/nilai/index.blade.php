@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold">📊 Kelola Nilai & Kelas</h1>
            <p class="text-muted small mb-0">Pilih folder kelas untuk menginput nilai atau mengelola status rapor.</p>
        </div>

        <div class="w-50 w-md-25">
            <input type="text" id="searchKelas" class="form-control rounded-pill shadow-sm border-0 px-4" placeholder="🔍 Cari Kelas atau Mapel...">
        </div>
    </div>

    <div class="row" id="kelasContainer">
        @forelse($allocations as $alloc)
        <div class="col-xl-4 col-md-6 mb-4 kelas-item" data-name="{{ strtolower($alloc->classroom->nama_kelas) }} {{ strtolower($alloc->subject->nama_mapel) }}">

            <div class="card border-0 shadow-sm h-100 hover-folder" style="border-radius: 15px; overflow: hidden;">

                <a href="{{ route('guru.nilai.create', $alloc->id) }}" class="text-decoration-none text-dark card-link-wrapper">
                    <div class="card-body py-4" style="border-left: 5px solid #f6c23e;">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    {{ $alloc->subject->nama_mapel }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alloc->classroom->nama_kelas }}</div>
                                <div class="mt-2 text-muted small">
                                    <i class="fas fa-edit me-1"></i> Klik untuk Input Nilai
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                                    <i class="fas fa-folder-open fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                @if(Auth::user()->teacher && Auth::user()->teacher->id == $alloc->classroom->wali_kelas_id)
                    <div class="card-footer bg-light border-0 py-2 px-3 position-relative">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted fw-bold fst-italic">
                                <i class="fas fa-user-tie me-1"></i> Anda Wali Kelas
                            </small>

                            <a href="{{ route('guru.wali-kelas.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm z-index-top">
                                <i class="fas fa-chalkboard-teacher me-1"></i> Kelola Rapor
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="alert alert-info d-inline-block shadow-sm px-5 py-4 rounded-4">
                <i class="fas fa-info-circle fa-2x mb-3 text-info"></i><br>
                <h5 class="fw-bold">Belum Ada Kelas</h5>
                <p class="mb-0">Anda belum memiliki jadwal mengajar. Silakan hubungi Admin.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div id="noResult" class="text-center py-5 d-none">
        <div class="opacity-50">
            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
            <p class="text-muted fw-bold">Kelas atau Mapel tidak ditemukan.</p>
        </div>
    </div>
</div>

{{-- SCRIPT LIVE SEARCH --}}
<script>
    document.getElementById('searchKelas').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.kelas-item');
        let visibleCount = 0;

        items.forEach(function(item) {
            let text = item.getAttribute('data-name');
            if (text.includes(filter)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        let noResult = document.getElementById('noResult');
        if (visibleCount === 0) noResult.classList.remove('d-none');
        else noResult.classList.add('d-none');
    });
</script>

{{-- CUSTOM CSS --}}
<style>
    .hover-folder {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background-color: #fff;
    }
    .hover-folder:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(246, 194, 62, 0.25) !important;
    }

    /* PENTING: Agar tombol Kelola Rapor bisa diklik */
    .card-footer {
        position: relative;
        z-index: 5;
    }
    .z-index-top {
        position: relative;
        z-index: 10;
    }
</style>
@endsection
