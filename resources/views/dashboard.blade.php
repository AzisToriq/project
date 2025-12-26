<x-app-layout>
    <x-slot name="header">
        <div class="d-none d-md-block"></div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">

            {{-- =================================================================== --}}
            {{-- 1. HERO SECTION (DYNAMIC GREETING) --}}
            {{-- =================================================================== --}}
            <div class="row mb-5">
                <div class="col-12">
                    @php
                        $role = Auth::user()->role;
                        $gradient = match($role) {
                            'admin'   => 'linear-gradient(120deg, #4facfe 0%, #00f2fe 100%)',
                            'teacher' => 'linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%)',
                            'parent'  => 'linear-gradient(120deg, #f6d365 0%, #fda085 100%)',
                            default   => 'linear-gradient(120deg, #a18cd1 0%, #fbc2eb 100%)',
                        };
                    @endphp

                    <div class="card border-0 rounded-4 text-white overflow-hidden position-relative shadow-lg"
                         style="background: {{ $gradient }}; min-height: 180px;">

                        <div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.2;">
                            <i class="fas fa-shapes fa-10x"></i>
                        </div>
                        <div class="position-absolute" style="bottom: -30px; left: 5%; opacity: 0.15;">
                            <i class="fas fa-wave-square fa-8x"></i>
                        </div>

                        <div class="card-body p-4 p-lg-5 position-relative z-1 d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center mb-3 mb-md-0">
                                <div class="bg-white bg-opacity-25 p-3 rounded-circle me-4 shadow-sm backdrop-blur border border-white border-opacity-50">
                                    @if($role === 'admin') <i class="fas fa-user-shield fa-3x"></i>
                                    @elseif($role === 'teacher') <i class="fas fa-chalkboard-teacher fa-3x"></i>
                                    @elseif($role === 'parent') <i class="fas fa-user-friends fa-3x"></i>
                                    @else <i class="fas fa-user-graduate fa-3x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="text-uppercase ls-2 mb-1 opacity-90 small fw-bold text-white-50">
                                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                    </h6>
                                    <h1 class="display-6 fw-bold mb-1 text-shadow">
                                        Halo, {{ Auth::user()->name }}! 👋
                                    </h1>
                                    <p class="mb-0 opacity-90 fw-light">
                                        @if($role == 'admin') Panel Kontrol Administrator.
                                        @elseif($role == 'teacher') Selamat mengajar hari ini!
                                        @elseif($role == 'parent') Pantau perkembangan buah hati Anda.
                                        @else Semangat belajar! Masa depan cerah menantimu.
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-end d-none d-lg-block bg-white bg-opacity-10 p-3 rounded-3 backdrop-blur border border-white border-opacity-25">
                                <div class="h1 fw-bold mb-0">{{ date('H:i') }}</div>
                                <div class="small text-uppercase fw-bold ls-1">WIB - Indonesia</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================== --}}
            {{-- 2. DASHBOARD CONTENT --}}
            {{-- =================================================================== --}}

            {{-- >>> ADMIN VIEW <<< --}}
            @if($role === 'admin')
                <div class="row g-4 mb-5">
                    {{-- Statistik Cards --}}
                    <div class="col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm h-100 hover-up bg-white">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><p class="text-muted small fw-bold text-uppercase mb-1">Total Siswa</p><h2 class="fw-bold text-dark mb-0">{{ $total_siswa ?? 0 }}</h2></div>
                                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3"><i class="fas fa-users"></i></div>
                                </div>
                                <div class="mt-3"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2"><i class="fas fa-check-circle me-1"></i> Data Aktif</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm h-100 hover-up bg-white">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><p class="text-muted small fw-bold text-uppercase mb-1">Total Guru</p><h2 class="fw-bold text-dark mb-0">{{ $total_guru ?? 0 }}</h2></div>
                                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3"><i class="fas fa-chalkboard-teacher"></i></div>
                                </div>
                                <div class="mt-3"><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2"><i class="fas fa-user-tie me-1"></i> Pengajar</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm h-100 hover-up bg-white">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><p class="text-muted small fw-bold text-uppercase mb-1">Total Kelas</p><h2 class="fw-bold text-dark mb-0">{{ $total_kelas ?? 0 }}</h2></div>
                                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-3"><i class="fas fa-chalkboard"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 rounded-4 shadow-sm h-100 hover-up bg-white">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><p class="text-muted small fw-bold text-uppercase mb-1">Mapel</p><h2 class="fw-bold text-dark mb-0">{{ $total_mapel ?? 0 }}</h2></div>
                                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3"><i class="fas fa-book"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-4 border-start border-4 border-primary ps-3">Menu Master Data</h5>
                <div class="row g-3">
                    @php
                        $menus = [
                            ['route' => 'guru.data.subjects.index', 'icon' => 'fa-book', 'color' => 'primary', 'label' => 'Mapel', 'desc' => 'Kelola Mapel'],
                            ['route' => 'guru.data.classrooms.index', 'icon' => 'fa-chalkboard', 'color' => 'success', 'label' => 'Kelas', 'desc' => 'Manajemen Kelas'],
                            ['route' => 'guru.data.teachers.index', 'icon' => 'fa-user-tie', 'color' => 'warning', 'label' => 'Guru', 'desc' => 'Data Pengajar'],
                            ['route' => 'guru.data.students.index', 'icon' => 'fa-user-graduate', 'color' => 'info', 'label' => 'Siswa', 'desc' => 'Data Peserta Didik'],
                            ['route' => 'guru.data.parents.index', 'icon' => 'fa-user-friends', 'color' => 'dark', 'label' => 'Wali Murid', 'desc' => 'Data Orang Tua'],
                            ['route' => 'guru.data.allocations.index', 'icon' => 'fa-project-diagram', 'color' => 'secondary', 'label' => 'Plotting', 'desc' => 'Distribusi Guru'],
                            ['route' => 'admin.schedule.index', 'icon' => 'fa-calendar-alt', 'color' => 'danger', 'label' => 'Jadwal', 'desc' => 'Susun Jadwal'],
                            ['route' => 'admin.spp.index', 'icon' => 'fa-file-invoice-dollar', 'color' => 'success', 'label' => 'Keuangan', 'desc' => 'SPP & Pembayaran'],
                        ];
                    @endphp
                    @foreach($menus as $menu)
                    <div class="col-md-4 col-xl-2">
                        <a href="{{ route($menu['route']) }}" class="text-decoration-none">
                            <div class="card border-0 rounded-4 shadow-sm h-100 hover-scale text-center py-4 bg-white">
                                <div class="card-body">
                                    <div class="avatar-lg bg-{{ $menu['color'] }} bg-opacity-10 text-{{ $menu['color'] }} rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas {{ $menu['icon'] }} fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $menu['label'] }}</h6>
                                    <small class="text-muted d-block lh-sm" style="font-size: 11px;">{{ $menu['desc'] }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

            {{-- >>> TEACHER VIEW <<< --}}
            @elseif($role === 'teacher')
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-4 shadow-sm h-100 bg-white">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-day text-primary me-2"></i>Jadwal Mengajar Hari Ini</h5>
                                <a href="{{ route('guru.jadwal.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold small text-primary border">Lihat Semua</a>
                            </div>
                            <div class="card-body p-4">
                                @if(isset($jadwal_hari_ini) && count($jadwal_hari_ini) > 0)
                                    <div class="timeline mt-2">
                                            @foreach($jadwal_hari_ini as $item)
                                            <div class="timeline-item pb-4 ps-4 border-start border-2 border-light position-relative">
                                                <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-white border-4 border-primary shadow-sm" style="width: 18px; height: 18px; border-style: solid;"></div>
                                                <div class="card border-0 bg-light rounded-3 p-3 ms-2 hover-bg-white transition-all shadow-sm-hover">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge bg-primary rounded-pill px-3 shadow-sm">{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}</span>
                                                        <span class="text-muted small fw-bold"><i class="fas fa-door-open me-1"></i> Kelas {{ optional($item->teacher_allocation->classroom)->nama_kelas }}</span>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1">{{ optional($item->teacher_allocation->subject)->nama_mapel }}</h6>
                                                    <div class="d-flex justify-content-between align-items-end mt-3">
                                                        <a href="{{ route('guru.absensi.input', $item->id) }}" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm fw-bold"><i class="fas fa-edit me-1"></i> Absen</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5"><div class="bg-light rounded-circle d-inline-flex p-4 mb-3"><i class="fas fa-coffee fa-3x text-secondary opacity-50"></i></div><h6 class="fw-bold text-dark">Tidak Ada Jadwal</h6></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 rounded-4 shadow-sm bg-gradient-primary text-white mb-4 position-relative overflow-hidden" style="background: linear-gradient(45deg, #3a7bd5, #3a6073);">
                            <div class="card-body p-4 position-relative z-1">
                                <h1 class="display-4 fw-bold mb-0">{{ $jumlah_kelas ?? 0 }}</h1>
                                <p class="mb-0 opacity-75 fw-bold text-uppercase ls-1 small">Kelas Diampu</p>
                            </div>
                            <i class="fas fa-layer-group position-absolute text-white" style="bottom: -20px; right: -20px; font-size: 100px; opacity: 0.1;"></i>
                        </div>
                        <div class="card border-0 rounded-4 shadow-sm bg-white">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2"><h6 class="fw-bold text-gray-800">Aksi Cepat</h6></div>
                            <div class="card-body p-2">
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('guru.absensi.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 p-3 mb-1 d-flex align-items-center hover-bg-light">
                                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3"><i class="fas fa-user-check fa-lg"></i></div><div><h6 class="fw-bold text-dark mb-0">Rekap Absensi</h6></div>
                                    </a>
                                    <a href="{{ route('guru.nilai.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 p-3 mb-1 d-flex align-items-center hover-bg-light">
                                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3"><i class="fas fa-star fa-lg"></i></div><div><h6 class="fw-bold text-dark mb-0">Input Nilai</h6></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- >>> PARENT (ORTU) VIEW - PERBAIKAN: HAPUS MENU GANDA, FOKUS PERKEMBANGAN <<< --}}
            @elseif($role === 'parent')
                <div class="row g-4">
                    {{-- 1. Profil Anak (KIRI) --}}
                    <div class="col-lg-4">
                        <div class="card border-0 rounded-4 shadow-sm h-100 bg-white">
                            <div class="card-body p-4 text-center">
                                @if(isset($anak))
                                    <div class="mb-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($anak->nama_lengkap) }}&background=random&size=128" class="rounded-circle shadow-sm" width="100">
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">{{ $anak->nama_lengkap }}</h5>
                                    <p class="text-muted mb-3">{{ $anak->nisn }} | Kelas {{ $anak->classroom->nama_kelas ?? '-' }}</p>
                                    <div class="d-grid gap-2">
                                        {{-- TOMBOL LIHAT PERKEMBANGAN (FOKUS UTAMA) --}}
                                        <a href="{{ route('ortu.perkembangan.index') }}" class="btn btn-primary rounded-pill btn-sm">
                                            <i class="fas fa-chart-line me-1"></i> Statistik & Perkembangan
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">Data anak belum terhubung. Hubungi Admin.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Menu Cepat + Tagihan SPP (KANAN) --}}
                    <div class="col-lg-8">
                        {{-- A. LINK BESAR KE PERKEMBANGAN ANAK (GANTI MENU GANDA TADI) --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <a href="{{ route('ortu.perkembangan.index') }}" class="text-decoration-none">
                                    <div class="card border-0 rounded-4 shadow-sm bg-white hover-up p-4 d-flex flex-row align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                                                <i class="fas fa-user-graduate fa-2x"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold text-dark mb-1">Lihat Perkembangan Anak</h5>
                                                <p class="text-muted mb-0 small">Cek Absensi, Nilai, dan Catatan Wali Kelas secara lengkap.</p>
                                            </div>
                                        </div>
                                        <div class="bg-light rounded-circle p-2 text-muted">
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        {{-- B. Daftar Tagihan SPP (HANYA YANG BELUM LUNAS) --}}
                        <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-dark"><i class="fas fa-file-invoice-dollar text-danger me-2"></i>Tagihan Belum Lunas</h6>
                                <a href="{{ route('ortu.tagihan.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold small text-muted">Lihat Semua</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4">Tagihan</th>
                                                <th>Nominal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- FILTER HANYA UNPAID DI VIEW --}}
                                            @php
                                                $unpaidBills = isset($tagihan_spp) ? $tagihan_spp->where('status', 'unpaid')->where('is_published', 1)->take(3) : collect([]);
                                            @endphp

                                            @forelse($unpaidBills as $tagihan)
                                            <tr>
                                                <td class="ps-4">
                                                    @if($tagihan->title)
                                                        <div class="fw-bold text-dark">{{ $tagihan->title }}</div>
                                                        <div class="small text-muted">{{ $tagihan->month }} {{ $tagihan->year }}</div>
                                                    @else
                                                        <div class="fw-bold text-dark">{{ $tagihan->month }} {{ $tagihan->year }}</div>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($tagihan->dispensation_status == 'pending')
                                                        <span class="badge bg-warning text-dark rounded-pill">Dispensasi Pending</span>
                                                    @elseif($tagihan->dispensation_status == 'approved')
                                                        <span class="badge bg-info text-dark rounded-pill">Dispensasi Disetujui</span>
                                                    @else
                                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Belum Bayar</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">
                                                    <i class="fas fa-check-circle text-success me-1"></i> Tidak ada tagihan tertunggak.
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- >>> STUDENT VIEW <<< --}}
            @elseif($role === 'student')
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-4 shadow-sm h-100 bg-white">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-book-open text-primary me-2"></i>Mata Pelajaran Hari Ini</h5>
                            </div>
                            <div class="card-body p-4">
                                @if(isset($jadwal_hari_ini) && count($jadwal_hari_ini) > 0)
                                    <div class="row g-3">
                                            @foreach($jadwal_hari_ini as $item)
                                            <div class="col-md-6">
                                                <div class="card border-0 bg-light rounded-4 h-100 hover-up transition-all shadow-sm">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="badge bg-white text-dark shadow-sm border border-light">{{ substr($item->start_time, 0, 5) }}</span>
                                                            <span class="badge bg-primary">{{ optional($item->teacher_allocation->subject)->code ?? 'MAPEL' }}</span>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-1 text-truncate">{{ optional($item->teacher_allocation->subject)->nama_mapel }}</h6>
                                                        <div class="d-flex align-items-center mt-3 pt-2 border-top border-white">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;"><i class="fas fa-user-tie" style="font-size: 12px;"></i></div>
                                                            <small class="text-muted fw-bold text-truncate" style="font-size: 12px;">{{ optional($item->teacher_allocation->teacher)->nama_lengkap }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5"><i class="fas fa-smile-beam fa-4x text-warning opacity-50 mb-3"></i><h6 class="fw-bold text-dark">Libur / Bebas Kelas!</h6><p class="text-muted small">Tidak ada jadwal pelajaran untuk hari ini.</p></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('siswa.rapor') }}" class="card border-0 rounded-4 shadow-sm text-decoration-none hover-scale overflow-hidden h-100 bg-white">
                                    <div class="card-body p-4 d-flex justify-content-between align-items-center position-relative">
                                        <div><h5 class="fw-bold text-dark mb-1">E-Rapor</h5><p class="text-muted small mb-0">Lihat Hasil Belajar</p></div>
                                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success z-1"><i class="fas fa-file-invoice fa-2x"></i></div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('siswa.jadwal') }}" class="card border-0 rounded-4 shadow-sm text-decoration-none hover-scale overflow-hidden h-100 bg-white">
                                    <div class="card-body p-4 d-flex justify-content-between align-items-center position-relative">
                                        <div><h5 class="fw-bold text-dark mb-1">Kalender</h5><p class="text-muted small mb-0">Jadwal Akademik</p></div>
                                        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info z-1"><i class="fas fa-calendar-week fa-2x"></i></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- CSS INJECTED --}}
    <style>
        .ls-2 { letter-spacing: 2px; }
        .ls-1 { letter-spacing: 1px; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .hover-up { transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); }
        .hover-up:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
        .hover-scale { transition: transform 0.2s ease; }
        .hover-scale:hover { transform: scale(1.02); }
        .hover-bg-light:hover { background-color: #f8f9fa !important; }
        .shadow-sm-hover:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
        .backdrop-blur { backdrop-filter: blur(10px); }
        .timeline-item:last-child { border-left: 0 !important; margin-left: 2px; }
    </style>
</x-app-layout>
