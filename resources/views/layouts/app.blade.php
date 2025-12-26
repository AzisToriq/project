<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Siskol') }}</title>

    {{-- Fonts & Design Assets --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* Color Palette Modern */
            --sidebar-bg: #0f172a;       /* Dark Slate */
            --sidebar-text: #94a3b8;     /* Muted Text */
            --sidebar-active: #3b82f6;   /* Bright Blue */
            --sidebar-hover: #1e293b;    /* Slightly Lighter Dark */
            --content-bg: #f1f5f9;       /* Light Gray Background */
            --header-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--content-bg);
            overflow-x: hidden;
            color: #334155;
        }

        #wrapper { display: flex; width: 100%; align-items: stretch; }

        /* --- SIDEBAR STYLING --- */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        #sidebar.active { margin-left: -260px; }

        #sidebar .sidebar-header {
            padding: 20px 25px;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        #sidebar ul.components { padding: 15px 15px; }

        .nav-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #475569;
            padding: 20px 10px 5px;
            letter-spacing: 0.05em;
        }

        #sidebar ul li { margin-bottom: 4px; }

        #sidebar ul li a {
            padding: 10px 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            border-radius: 8px;
            font-weight: 500;
        }

        #sidebar ul li a i {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 10px;
            text-align: center;
            transition: 0.2s;
        }

        /* Hover Effect */
        #sidebar ul li a:hover {
            background: var(--sidebar-hover);
            color: #fff;
            transform: translateX(3px);
        }

        /* Active State */
        #sidebar ul li.active > a {
            background: var(--sidebar-active);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); /* Blue Glow */
        }

        #sidebar ul li.active > a i { color: #fff; }

        /* --- NAVBAR STYLING --- */
        .navbar {
            background: var(--header-bg) !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .btn-toggle-sidebar {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #64748b;
            border-radius: 8px;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .btn-toggle-sidebar:hover { background: #f8fafc; color: var(--sidebar-active); border-color: var(--sidebar-active); }

        /* --- CONTENT AREA --- */
        #content { width: 100%; transition: all 0.3s; }
        .main-container { padding: 30px; }

        /* --- UTILS --- */
        .avatar-img { object-fit: cover; width: 100%; height: 100%; }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; position: fixed; height: 100%; }
            #sidebar.active { margin-left: 0; }
            #sidebar-overlay.active { display: block; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 999; backdrop-filter: blur(2px); }
        }
        #sidebar-overlay { display: none; }

        .blink-soft { animation: blinker 1.5s linear infinite; }
        @keyframes blinker { 50% { opacity: 0.6; } }
    </style>
</head>
<body>

    <div id="wrapper">
        {{-- SIDEBAR --}}
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-lg" style="width: 40px; height: 40px;">
                        <i class="fas fa-graduation-cap text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="m-0 fw-bold text-white" style="letter-spacing: 1px;">SISKOL</h5>
                        <small class="text-muted" style="font-size: 0.75rem;">Sistem Akademik</small>
                    </div>
                </div>
            </div>

            <ul class="list-unstyled components">
                {{-- DASHBOARD --}}
                <div class="nav-label mt-2">Menu Utama</div>

                @php
                    // Tentukan route dashboard berdasarkan role (SAFE MODE)
                    $homeRoute = 'dashboard'; // Default (Admin/Guru)

                    if(auth()->user()->role == 'parent' && Route::has('ortu.dashboard')) {
                        $homeRoute = 'ortu.dashboard';
                    } elseif(auth()->user()->role == 'student' && Route::has('siswa.dashboard')) {
                        $homeRoute = 'siswa.dashboard'; // Asumsi, jika tidak ada fallback ke 'dashboard'
                    }
                @endphp

                <li class="{{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                    <a href="{{ route($homeRoute) }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                {{-- ROLE: ADMINISTRATOR --}}
                @auth
                @if(auth()->user()->role === 'admin')
                    {{-- DATA MASTER --}}
                    <div class="nav-label">Data Master</div>

                    <li class="{{ request()->routeIs('guru.data.subjects.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.data.subjects.index') }}">
                            <i class="fas fa-book"></i> Mata Pelajaran
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('guru.data.classrooms.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.data.classrooms.index') }}">
                            <i class="fas fa-chalkboard"></i> Data Kelas
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('guru.data.students.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.data.students.index') }}">
                            <i class="fas fa-user-graduate"></i> Data Siswa
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('guru.data.teachers.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.data.teachers.index') }}">
                            <i class="fas fa-chalkboard-teacher"></i> Data Guru
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('guru.data.parents.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.data.parents.index') }}">
                            <i class="fas fa-user-tie"></i> Data Wali Murid
                        </a>
                    </li>

                    {{-- AKADEMIK --}}
                    <div class="nav-label">Akademik</div>
                    <li class="{{ request()->routeIs('guru.data.allocations.index') ? 'active' : '' }}">
                        <a href="{{ route('guru.data.allocations.index') }}">
                            <i class="fas fa-sitemap"></i> Plotting Guru
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.schedule.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.schedule.index') }}">
                            <i class="far fa-calendar-alt"></i> Jadwal Sekolah
                        </a>
                    </li>

                    {{-- KEUANGAN / PAYMENT --}}
                    <div class="nav-label">Keuangan Sekolah</div>

                    {{-- 1. Data Tagihan (Utama) --}}
                    <li class="{{ request()->routeIs('admin.spp.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.spp.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i> Data Tagihan SPP
                        </a>
                    </li>

                    {{-- VERIFIKASI PEMBAYARAN (ADMIN) --}}
                    <li class="{{ request()->routeIs('admin.spp.verification') ? 'active' : '' }}">
                        <a href="{{ route('admin.spp.verification') }}" class="nav-link">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <span>
                                    <i class="fas fa-check-double me-2"></i> Verifikasi
                                </span>

                                {{-- Logic Badge Pending --}}
                                @php
                                    $pendingCount = \App\Models\SppPayment::where('status', 'pending')->count();
                                @endphp

                                @if($pendingCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-2 fw-bold" style="font-size: 0.7rem;">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    </li>

                    {{-- 3. Arsip Lunas --}}
                    <li class="{{ request()->routeIs('admin.spp.archive') ? 'active' : '' }}">
                        <a href="{{ route('admin.spp.archive') }}">
                            <i class="fas fa-archive"></i> Arsip Lunas
                        </a>
                    </li>
                @endif

                {{-- ROLE: TEACHER --}}
                @if(auth()->user()->role === 'teacher')
                    <div class="nav-label">Aktivitas Mengajar</div>
                    <li class="{{ request()->routeIs('guru.jadwal*') ? 'active' : '' }}">
                        <a href="{{ route('guru.jadwal.index') }}">
                            <i class="far fa-clock"></i> Jadwal Saya
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.absensi.index') }}">
                            <i class="fas fa-user-check"></i> Absensi Siswa
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('guru.nilai.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.nilai.index') }}">
                            <i class="fas fa-marker"></i> Input Nilai
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('guru.tugas.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.tugas.index') }}">
                            <i class="fas fa-laptop-code"></i> E-Learning
                        </a>
                    </li>

                    {{-- WALI KELAS --}}
                    <div class="nav-label">Wali Kelas</div>
                    <li class="{{ request()->routeIs('guru.wali-kelas.*') ? 'active' : '' }}">
                        <a href="{{ route('guru.wali-kelas.index') }}">
                            <i class="fas fa-clipboard-check"></i> Monitoring Kelas
                        </a>
                    </li>
                @endif

                {{-- ROLE: STUDENT --}}
                @if(auth()->user()->role === 'student')
                    <div class="nav-label">Area Belajar</div>
                    <li class="{{ request()->routeIs('siswa.jadwal') ? 'active' : '' }}">
                        <a href="{{ route('siswa.jadwal') }}">
                            <i class="far fa-calendar-check"></i> Jadwal
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('siswa.tugas.*') ? 'active' : '' }}">
                        <a href="{{ route('siswa.tugas.index') }}">
                            <i class="fas fa-tasks"></i> Tugas & Materi
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('siswa.rapor') ? 'active' : '' }}">
                        <a href="{{ route('siswa.rapor') }}">
                            <i class="fas fa-file-invoice"></i> Rapor Saya
                        </a>
                    </li>
                @endif

                {{-- ROLE: PARENT (ORTU) --}}
                @if(auth()->user()->role === 'parent')

                    {{-- LOGIC: Hitung Tagihan Belum Lunas (Unpaid) --}}
                    @php
                        $unpaidCount = 0;
                        $myStudent = \App\Models\Student::where('parent_user_id', auth()->id())->first();

                        if($myStudent) {
                            $unpaidCount = \App\Models\SppPayment::where('student_id', $myStudent->id)
                                            ->where('status', 'unpaid')
                                            ->where('is_published', 1) // FIX: HANYA YANG SUDAH TERBIT
                                            ->count();
                        }
                    @endphp

                    <div class="nav-label">Wali Murid</div>

                    <li class="{{ request()->routeIs('ortu.perkembangan.*') ? 'active' : '' }}">
                        <a href="{{ route('ortu.perkembangan.index') }}">
                            <i class="fas fa-child"></i> Perkembangan Anak
                        </a>
                    </li>

                    {{-- LINK BAYAR SPP DENGAN BADGE --}}
                    <li class="{{ request()->routeIs('ortu.tagihan.*') ? 'active' : '' }}">
                        <a href="{{ route('ortu.tagihan.index') }}" class="d-flex justify-content-between align-items-center pe-3">
                            <span>
                                <i class="fas fa-wallet"></i> Pembayaran SPP
                            </span>

                            {{-- Tampilkan Badge Merah jika ada tunggakan --}}
                            @if($unpaidCount > 0)
                                <span class="badge bg-danger rounded-pill fw-bold blink-soft" style="font-size: 0.7rem;">
                                    {{ $unpaidCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endif

                @endauth

                <div class="nav-label">Pengaturan</div>
                <li class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <a href="{{ route('profile.edit') }}">
                        <i class="fas fa-cog"></i> Profil Saya
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="text-danger">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </a>
                    </form>
                </li>
            </ul>
        </nav>

        <div id="sidebar-overlay"></div>

        {{-- MAIN CONTENT --}}
        <div id="content">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-toggle-sidebar">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="text-end d-none d-sm-block">
                            <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ auth()->user()->name ?? 'Guest' }}</p>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2" style="font-size: 0.65rem;">
                                {{ strtoupper(auth()->user()->role ?? 'USER') }}
                            </span>
                        </div>
                        <div class="rounded-circle bg-white border p-1 shadow-sm" style="width: 42px; height: 42px; cursor: pointer;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=3b82f6&color=fff&bold=true" class="avatar-img rounded-circle">
                        </div>
                    </div>
                </div>
            </nav>

            <div class="main-container">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar Toggler
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('sidebarCollapse');

            if(toggleBtn){
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
            }

            if(overlay){
                overlay.addEventListener('click', () => {
                    sidebar.classList.add('active');
                    overlay.classList.remove('active');
                });
            }

            // --- NOTIFIKASI POPUP TENGAH ---
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    position: 'center',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    position: 'center',
                    showConfirmButton: true,
                    confirmButtonColor: '#d33'
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
