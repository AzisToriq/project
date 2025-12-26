@extends('layouts.app')

@section('content')
<div class="py-4">
    {{-- HEADER: JUDUL & SEARCH --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold mb-1">📁 Absensi Kelas</h1>
            <p class="text-muted small mb-0">Pilih folder kelas untuk melihat mata pelajaran yang tersedia.</p>
        </div>

        <div class="w-100 w-md-50" style="max-width: 400px;">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                <span class="input-group-text bg-white border-0 ps-3">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="searchKelas" class="form-control border-0 py-2" placeholder="Cari Kelas..." autocomplete="off">
            </div>
        </div>
    </div>

    {{-- GRID KELAS --}}
    <div class="row" id="kelasContainer">
        @forelse($classrooms as $c)
        <div class="col-xl-3 col-md-6 mb-4 kelas-item" data-name="{{ strtolower($c->nama_kelas) }}">

            {{-- LINK MENUJU HALAMAN LIST MAPEL --}}
            <a href="{{ route('guru.absensi.mapel', $c->id) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 py-3 hover-folder position-relative overflow-hidden" style="border-radius: 16px;">

                    {{-- Gari Biru di Kiri (Aksen Folder) --}}
                    <div class="position-absolute start-0 top-0 bottom-0 bg-primary" style="width: 6px;"></div>

                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2 ps-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="letter-spacing: 1px;">
                                    Folder Kelas
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-dark text-truncate">{{ $c->nama_kelas }}</div>
                                <div class="mt-2 text-muted small">
                                    <i class="fas fa-folder-open me-1"></i> Klik untuk buka
                                </div>
                            </div>
                            <div class="col-auto pe-2">
                                <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-folder fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        {{-- TAMPILAN JIKA KOSONG --}}
        <div class="col-12 text-center py-5">
             <div class="d-inline-block p-4 rounded-circle bg-light mb-3">
                <i class="fas fa-folder-open fa-3x text-muted opacity-50"></i>
             </div>
             <h5 class="text-muted fw-bold">Belum ada kelas</h5>
             <p class="text-muted small">Anda belum memiliki jadwal mengajar di kelas manapun.</p>
        </div>
        @endforelse
    </div>

    {{-- STATE JIKA HASIL PENCARIAN KOSONG --}}
    <div id="noResult" class="text-center py-5 d-none">
        <i class="fas fa-search text-muted opacity-25 fa-3x mb-3"></i>
        <p class="text-muted">Folder kelas tidak ditemukan.</p>
    </div>
</div>

{{-- SCRIPT PENCARIAN LIVE --}}
<script>
    document.getElementById('searchKelas').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.kelas-item');
        let visibleCount = 0;

        items.forEach(function(item) {
            let text = item.getAttribute('data-name');
            if (text.includes(filter)) {
                item.style.display = ''; // Munculkan
                visibleCount++;
            } else {
                item.style.display = 'none'; // Sembunyikan
            }
        });

        // Toggle pesan kosong
        let noResult = document.getElementById('noResult');
        if (visibleCount === 0) noResult.classList.remove('d-none');
        else noResult.classList.add('d-none');
    });
</script>

{{-- STYLING TAMBAHAN --}}
<style>
    /* Animasi Hover Folder */
    .hover-folder {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background-color: #fff;
    }
    .hover-folder:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(78, 115, 223, 0.15) !important;
        background-color: #f8f9fc;
    }

    /* Icon Bulat */
    .icon-circle {
        height: 60px;
        width: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s;
    }

    /* Efek Icon Goyang Pas Hover */
    .hover-folder:hover .icon-circle {
        transform: scale(1.1) rotate(-10deg);
    }
</style>
@endsection
