@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="row align-items-end mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-gray-800 font-weight-bold mb-1">📁 Direktori Siswa</h1>
            <p class="text-muted small mb-0">
                Total <b>{{ $classrooms->count() }}</b> Kelas &
                <b>{{ $classrooms->sum('students_count') }}</b> Siswa Terdaftar.
            </p>
        </div>
        <div class="col-md-4 mt-3 mt-md-0">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0 ps-3">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="searchClass" class="form-control border-start-0 ps-2" placeholder="Cari nama kelas..." style="height: 45px;">
            </div>
        </div>
    </div>

    <div class="row" id="classContainer">
        @forelse($classrooms as $c)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4 class-item">
            <a href="{{ route('guru.data.students.show_class', $c->id) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-lift position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-folder-open fa-lg"></i>
                            </div>
                            <span class="badge bg-light text-secondary border">
                                Level {{ $c->level }}
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-1 class-name">{{ $c->nama_kelas }}</h5>

                        <div class="text-muted small mb-3 text-truncate">
                            <i class="fas fa-chalkboard-teacher me-1 text-primary opacity-50"></i>
                            {{ $c->waliKelas->nama_lengkap ?? 'Belum ada Wali Kelas' }}
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-2">
                            <div class="d-flex align-items-center text-secondary small">
                                <i class="fas fa-users me-2"></i>
                                <span class="fw-bold text-dark">{{ $c->students_count }}</span>
                                <span class="ms-1">Siswa</span>
                            </div>
                            <div class="small text-primary fw-bold">
                                Buka <i class="fas fa-arrow-right ms-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
                <div class="mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-folder-plus fa-3x"></i>
                    </div>
                </div>
                <h5 class="fw-bold">Belum Ada Data Kelas</h5>
                <p class="text-muted">Silakan tambahkan data kelas terlebih dahulu di menu Data Master.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div id="noResult" class="d-none text-center py-5">
        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50">
        <p class="text-muted">Kelas yang Anda cari tidak ditemukan.</p>
    </div>
</div>

<script>
    document.getElementById('searchClass').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.class-item');
        let visibleCount = 0;

        items.forEach(function(item) {
            let name = item.querySelector('.class-name').innerText.toLowerCase();
            if (name.includes(filter)) {
                item.style.display = ''; // Show
                visibleCount++;
            } else {
                item.style.display = 'none'; // Hide
            }
        });

        // Tampilkan pesan jika tidak ada hasil
        let noResult = document.getElementById('noResult');
        if (visibleCount === 0 && filter !== '') {
            noResult.classList.remove('d-none');
        } else {
            noResult.classList.add('d-none');
        }
    });
</script>

<style>
    /* Efek Hover Profesional */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15) !important; /* Bayangan halus biru */
    }
    .hover-lift:active {
        transform: translateY(-2px);
    }
</style>
@endsection
