@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-gray-800 font-weight-bold">📅 Jadwal Pelajaran</h1>
            <p class="text-muted small mb-0">Pilih kelas untuk melihat dan mengatur jadwal pelajaran.</p>
        </div>
        <div class="col-md-4">
            <input type="text" id="searchClass" class="form-control rounded-pill border-0 shadow-sm ps-4" placeholder="Cari Kelas...">
        </div>
    </div>

    <div class="row" id="classContainer">
        @forelse($classrooms as $c)
        <div class="col-xl-3 col-md-6 mb-4 class-item">
            <a href="{{ route('admin.schedule.show', $c->id) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="far fa-calendar-alt fa-lg"></i>
                            </div>
                            <span class="badge bg-light text-secondary border">Level {{ $c->level }}</span>
                        </div>

                        <h5 class="fw-bold text-dark mb-1 class-name">{{ $c->nama_kelas }}</h5>
                        <p class="text-muted small mb-0">
                            Kelola jadwal senin s.d sabtu
                        </p>
                    </div>
                    <div class="card-footer bg-light border-0 py-2 text-center">
                        <small class="fw-bold text-primary">Lihat Jadwal <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="opacity-50">
                <i class="fas fa-chalkboard fa-3x mb-3 text-muted"></i>
                <p class="text-muted">Belum ada data kelas.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<script>
    document.getElementById('searchClass').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('.class-item').forEach(item => {
            let name = item.querySelector('.class-name').innerText.toLowerCase();
            item.style.display = name.includes(filter) ? '' : 'none';
        });
    });
</script>

<style>
    .hover-card { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1)!important; }
</style>
@endsection
