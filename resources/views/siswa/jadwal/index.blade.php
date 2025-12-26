@extends('layouts.app')

@section('content')
<div class="py-4">
    {{--
        SETUP VARIABLE PHP
    --}}
    @php
        // 0. Safety Check: Pastikan variable ada
        $schedules = $schedules ?? collect([]);

        // 1. Mapping Hari & Bulan Indonesia
        $dayMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $monthMap = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
            'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus',
            'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];

        // 2. Ambil Waktu Saat Ini
        $todayEnglish = date('l');
        $todayDate    = date('d');
        $monthEnglish = date('F');
        $year         = date('Y');

        // 3. Translate
        $hariIniIndo  = $dayMap[$todayEnglish] ?? 'Senin';
        $bulanIniIndo = $monthMap[$monthEnglish] ?? 'Januari';
        $tanggalFull  = "$hariIniIndo, $todayDate $bulanIniIndo $year";

        // 4. List Hari untuk Looping Card
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // 5. Ambil Nama Kelas (Safe Check)
        $namaKelas = 'Belum Masuk Kelas';
        if(Auth::user()->student && Auth::user()->student->classroom) {
            // Sesuaikan 'nama_kelas' atau 'name' dengan kolom di db kamu
            $namaKelas = Auth::user()->student->classroom->nama_kelas ?? Auth::user()->student->classroom->name;
        }
    @endphp

    {{-- HEADER HALAMAN --}}
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
            <i class="fas fa-calendar-alt fa-lg"></i>
        </div>
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold mb-0">Jadwal Pelajaran</h1>
            <p class="text-muted small mb-0">
                <span class="text-dark fw-bold">
                    <i class="fas fa-school me-1"></i> Kelas: {{ $namaKelas }}
                </span>
                <span class="mx-2">|</span>
                <span class="text-secondary">
                    <i class="far fa-clock me-1"></i> {{ $tanggalFull }}
                </span>
            </p>
        </div>
    </div>

    <div class="row">
        {{-- LOOPING KARTU HARI SENIN - SABTU --}}
        @foreach($days as $day)
            @php
                // Filter data jadwal dari Collection utama berdasarkan Hari
                $dailySchedules = $schedules->where('day', $day);
            @endphp

            {{-- Hanya tampilkan Card jika hari itu ada jadwal --}}
            @if($dailySchedules->count() > 0)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">

                    {{-- Header Card Hari --}}
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-primary mb-0 text-uppercase">{{ $day }}</h6>

                        {{-- Badge Hari Ini --}}
                        @if($hariIniIndo == $day)
                            <span class="badge bg-warning text-dark animate-pulse shadow-sm" style="font-size: 10px;">
                                <i class="fas fa-circle fa-xs me-1"></i> HARI INI
                            </span>
                        @endif
                    </div>

                    {{-- Body Card Jadwal --}}
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($dailySchedules as $item)
                            <div class="list-group-item px-4 py-3 border-0 border-bottom d-flex align-items-center hover-bg-light">

                                {{-- A. JAM PELAJARAN --}}
                                <div class="text-center me-3" style="width: 60px;">
                                    <span class="d-block fw-bold text-dark" style="font-family: monospace; font-size: 14px;">
                                        {{ $item->start_time->format('H:i') }}
                                    </span>
                                    <small class="text-muted" style="font-size: 10px;">
                                        {{ $item->end_time->format('H:i') }}
                                    </small>
                                </div>

                                {{-- B. MAPEL & GURU --}}
                                <div class="border-start ps-3 border-3 border-info w-100">
                                    {{-- Nama Mapel --}}
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14px;">
                                        {{ $item->teacher_allocation->subject->name ?? 'Mapel Kosong' }}
                                    </h6>

                                    {{-- Nama Guru (Logic Prioritas) --}}
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                        <i class="fas fa-user-tie me-1" style="font-size: 10px;"></i>
                                        @php
                                            $guru = $item->teacher_allocation->teacher ?? null;
                                            $namaGuru = 'Guru Belum Diset';

                                            if ($guru) {
                                                // Prioritas 1: Nama Akun User (Karena biasanya ini yg paling update)
                                                if ($guru->user && !empty($guru->user->name)) {
                                                    $namaGuru = $guru->user->name;
                                                }
                                                // Prioritas 2: Nama Lengkap di Tabel Teacher
                                                elseif (!empty($guru->nama_lengkap)) {
                                                    $namaGuru = $guru->nama_lengkap;
                                                }
                                                // Prioritas 3: Nama biasa
                                                elseif (!empty($guru->name)) {
                                                    $namaGuru = $guru->name;
                                                }
                                            }
                                        @endphp
                                        {{ $namaGuru }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

        {{-- STATE KOSONG (JIKA TOTAL JADWAL 0) --}}
        @if($schedules->isEmpty())
        <div class="col-12 text-center py-5">
            <div class="d-inline-block p-4 rounded-circle bg-light mb-3">
                <i class="fas fa-calendar-times fa-3x text-muted opacity-50"></i>
            </div>
            <h5 class="text-muted fw-bold">Tidak ada jadwal pelajaran ditemukan.</h5>
            <p class="text-muted small">Pastikan Anda sudah masuk ke dalam kelas atau hubungi Admin.</p>
        </div>
        @endif
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: #f8f9fc; transition: 0.2s; }

    /* Animasi denyut halus */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .8; transform: scale(1.05); }
    }
</style>
@endsection
