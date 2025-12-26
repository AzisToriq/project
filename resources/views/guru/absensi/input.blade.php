@extends('layouts.app')

@section('content')
<div class="py-4">

    {{-- HEADER & NAVIGASI --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        {{-- Tombol Kembali ke List Mapel --}}
        <a href="{{ route('guru.absensi.mapel', $allocation->classroom_id) }}" class="btn btn-link text-secondary pl-0 text-decoration-none fw-bold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>

        {{-- Judul Mapel --}}
        <div class="text-end">
            <h5 class="font-weight-bold text-dark mb-0">{{ $allocation->subject->nama_mapel }}</h5>
            <span class="badge bg-light text-secondary border">Kelas {{ $allocation->classroom->nama_kelas }}</span>
        </div>
    </div>

    {{-- ALERT STATUS JADWAL (LOGIC PINTAR) --}}
    @if(isset($statusJadwal) && $statusJadwal == 'MANUAL')
        <div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert">
            <div class="fs-1 me-3 text-warning"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <h6 class="fw-bold mb-1">Perhatian: Absensi di Luar Jadwal</h6>
                <p class="mb-0 small">
                    Hari ini ({{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l') }}) tidak tercatat jadwal untuk mapel ini.
                    Data akan disimpan sebagai <b>Absensi Manual/Susulan</b>.
                </p>
            </div>
        </div>
    @else
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4 px-4 py-3">
            <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
            <div>
                <h6 class="fw-bold mb-0">Sesuai Jadwal Mengajar</h6>
                <small class="text-muted">Absensi hari ini tercatat resmi sesuai jadwal.</small>
            </div>
        </div>
    @endif

    {{-- FORM INPUT UTAMA --}}
    <form action="{{ route('guru.absensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="teacher_allocation_id" value="{{ $allocation->id }}">
        <input type="hidden" name="classroom_id" value="{{ $allocation->classroom_id }}">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            {{-- TOOLBAR: TANGGAL & MAGIC BUTTON --}}
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        {{-- Date Picker Auto Reload --}}
                        <div class="d-inline-flex align-items-center bg-light rounded-pill px-3 py-2 border">
                            <i class="far fa-calendar-alt text-muted me-2"></i>
                            <input type="date" name="date" class="form-control form-control-sm border-0 bg-transparent fw-bold p-0 text-dark"
                                   value="{{ $tanggal }}" onchange="changeDate(this.value)" style="width: 130px; outline: none;">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        {{-- Tombol Set Semua Hadir --}}
                        <button type="button" onclick="setAllHadir()" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                            <i class="fas fa-check-double me-1"></i> Tandai Semua Hadir
                        </button>
                    </div>
                </div>
            </div>

            {{-- TABEL SISWA --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase small text-muted">
                            <tr>
                                <th class="px-4 py-3" width="50">No</th>
                                <th width="250">Nama Siswa</th>
                                <th class="text-center" width="300">Status Kehadiran</th>
                                <th class="px-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                            @php
                                // Ambil status lama atau default 'H' (Hadir)
                                $status = $existingAbsensi[$student->id]->status ?? 'H';
                                $note = $existingAbsensi[$student->id]->note ?? '';
                            @endphp
                            <tr>
                                <td class="px-4 text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $student->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $student->nis ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    {{-- Input Hidden --}}
                                    <input type="hidden" name="absensi[{{ $student->id }}][status]" id="status_{{ $student->id }}" value="{{ $status }}">

                                    {{-- TOMBOL WARNA WARNI (STYLE USER) --}}
                                    <div class="btn-group shadow-sm" role="group">
                                        <button type="button" onclick="setAbsen({{ $student->id }}, 'H')" id="btn_H_{{ $student->id }}"
                                            class="btn btn-sm px-3 {{ $status == 'H' ? 'btn-success' : 'btn-outline-success' }} fw-bold">Hadir</button>

                                        <button type="button" onclick="setAbsen({{ $student->id }}, 'S')" id="btn_S_{{ $student->id }}"
                                            class="btn btn-sm px-3 {{ $status == 'S' ? 'btn-primary' : 'btn-outline-primary' }} fw-bold">Sakit</button>

                                        <button type="button" onclick="setAbsen({{ $student->id }}, 'I')" id="btn_I_{{ $student->id }}"
                                            class="btn btn-sm px-3 {{ $status == 'I' ? 'btn-warning' : 'btn-outline-warning' }} fw-bold">Izin</button>

                                        <button type="button" onclick="setAbsen({{ $student->id }}, 'A')" id="btn_A_{{ $student->id }}"
                                            class="btn btn-sm px-3 {{ $status == 'A' ? 'btn-danger' : 'btn-outline-danger' }} fw-bold">Alpha</button>
                                    </div>
                                </td>
                                <td class="px-4">
                                    <input type="text" name="absensi[{{ $student->id }}][note]"
                                           class="form-control form-control-sm bg-light border-0 rounded-pill px-3"
                                           placeholder="Keterangan..." value="{{ $note }}">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0">Tidak ada siswa di kelas ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER SIMPAN --}}
            <div class="card-footer bg-white p-4 text-end border-top">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow rounded-pill hover-scale fw-bold">
                    <i class="fas fa-save me-2"></i> Simpan Data
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. Ganti Tanggal (Reload Page)
    function changeDate(date) {
        let currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('date', date);
        window.location.href = currentUrl.toString();
    }

    // 2. Logic Tombol Warna-Warni
    function setAbsen(studentId, status) {
        // Update input hidden
        document.getElementById('status_' + studentId).value = status;

        const types = ['H', 'S', 'I', 'A'];
        const colors = {'H':'success', 'S':'primary', 'I':'warning', 'A':'danger'};

        // Reset semua tombol jadi outline
        types.forEach(type => {
            let btn = document.getElementById('btn_' + type + '_' + studentId);
            let color = colors[type];
            if(btn) {
                btn.classList.remove('btn-' + color);
                btn.classList.add('btn-outline-' + color);
            }
        });

        // Set tombol aktif jadi solid
        let activeBtn = document.getElementById('btn_' + status + '_' + studentId);
        let activeColor = colors[status];
        if(activeBtn) {
            activeBtn.classList.remove('btn-outline-' + activeColor);
            activeBtn.classList.add('btn-' + activeColor);
        }
    }

    // 3. Magic Button: Set Semua Hadir
    function setAllHadir() {
        @foreach($students as $s)
            setAbsen({{ $s->id }}, 'H');
        @endforeach
    }
</script>

<style>
    .hover-scale:hover { transform: translateY(-2px); transition: 0.2s; }
</style>
@endsection
