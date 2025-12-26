@extends('layouts.app')

@section('content')
<div class="py-4">
    {{-- HEADER: Navigasi dan Ganti Kelas Cepat --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.schedule.index') }}" class="btn btn-light rounded-circle shadow-sm me-3 text-secondary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Jadwal Kelas {{ $classroom->nama_kelas }}</h1>
                <p class="text-muted small mb-0">Tahun Ajaran {{ $classroom->academic_year }}</p>
            </div>
        </div>

        <div class="d-flex gap-2">
            {{-- Dropdown Ganti Kelas --}}
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm dropdown-toggle rounded-pill px-4" type="button" id="classDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-exchange-alt me-2 text-muted"></i> Ganti Kelas
                </button>
                <ul class="dropdown-menu shadow border-0" aria-labelledby="classDropdown" style="max-height: 300px; overflow-y: auto;">
                    @foreach($allClassrooms as $c)
                        <li>
                            <a class="dropdown-item {{ $c->id == $classroom->id ? 'active' : '' }}" href="{{ route('admin.schedule.show', $c->id) }}">
                                {{ $c->nama_kelas }} (Level {{ $c->level }})
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <button class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                <i class="fas fa-plus me-2"></i> Tambah Jadwal
            </button>
        </div>
    </div>

    {{-- GRID JADWAL MINGGUAN --}}
    <div class="row">
        @php
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        @endphp

        @foreach($days as $day)
        @php
            // Menggunakan get() agar aman jika hari tersebut belum ada jadwal (mencegah error key undefined)
            $dailySchedules = $groupedSchedule->get($day, collect([]));
        @endphp

        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark m-0 text-uppercase" style="letter-spacing: 1px;">{{ $day }}</h6>
                    <span class="badge bg-light text-muted border">{{ $dailySchedules->count() }}</span>
                </div>
                <div class="card-body bg-light p-3">
                    @forelse($dailySchedules as $schedule)
                        <div class="bg-white p-3 mb-2 rounded-3 border-start border-4 border-primary shadow-sm position-relative hover-reveal">

                            {{-- TOMBOL AKSI: Muncul saat hover --}}
                            <div class="position-absolute top-0 end-0 mt-2 me-2 d-flex gap-1 action-buttons" style="opacity: 0; transition: opacity 0.2s; z-index: 5;">
                                <button class="btn btn-sm btn-light text-warning p-1 shadow-sm border"
                                        onclick="editSchedule({{ $schedule->id }}, '{{ $schedule->day }}', '{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}')"
                                        title="Edit Jam">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>

                                <form action="{{ route('admin.schedule.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger p-1 shadow-sm border"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>

                            {{-- VISUAL JAM PELAJARAN --}}
                            <div class="mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 rounded-pill px-3 py-1">
                                    <i class="far fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </span>
                            </div>

                            <h6 class="fw-bold text-dark mb-1">{{ $schedule->teacher_allocation->subject->nama_mapel ?? 'Mapel Terhapus' }}</h6>
                            <div class="d-flex align-items-center text-muted small">
                                <div class="avatar-xs bg-secondary bg-opacity-25 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                {{ $schedule->teacher_allocation->teacher->nama_lengkap ?? 'Guru Terhapus' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 opacity-25">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <p class="small mb-0">Tidak ada jadwal</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- MODAL TAMBAH JADWAL --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.schedule.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classroom->id }}">

                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Jadwal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mata Pelajaran & Guru</label>
                        <select name="teacher_allocation_id" class="form-select bg-light border-0" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($allocations as $alloc)
                                <option value="{{ $alloc->id }}">
                                    {{ $alloc->subject->nama_mapel }} ({{ $alloc->teacher->nama_lengkap }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Hari</label>
                        <select name="day" class="form-select bg-light border-0" required>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Mulai</label>
                            <input type="time" name="start_time" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Selesai</label>
                            <input type="time" name="end_time" class="form-control bg-light border-0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT JADWAL --}}
<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="editScheduleForm" method="POST">
                @csrf @method('PUT')

                <div class="modal-header bg-warning text-white border-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-clock me-2"></i>Edit Jam Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning small border-0 bg-warning bg-opacity-10 text-dark mb-4">
                        <i class="fas fa-info-circle me-1"></i> Hanya dapat mengubah Hari dan Jam.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Hari</label>
                        <select name="day" id="editDay" class="form-select bg-light border-0" required>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Mulai</label>
                            <input type="time" name="start_time" id="editStartTime" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Selesai</label>
                            <input type="time" name="end_time" id="editEndTime" class="form-control bg-light border-0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold">Update Jam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .hover-reveal:hover .action-buttons { opacity: 1 !important; }
</style>

<script>
    function editSchedule(id, day, start, end) {
        // Set Link URL Update
        let url = "{{ route('admin.schedule.update', ':id') }}";
        url = url.replace(':id', id);
        document.getElementById('editScheduleForm').action = url;

        // Pasang data ke form
        document.getElementById('editDay').value = day;
        document.getElementById('editStartTime').value = start;
        document.getElementById('editEndTime').value = end;

        // Munculkan Modal
        new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
    }
</script>
@endsection
