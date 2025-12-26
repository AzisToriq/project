<nav class="w-64 bg-white border-r border-gray-200 flex-shrink-0 hidden md:flex md:flex-col h-screen sticky top-0">

    <div class="h-16 flex items-center px-6 border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <span class="font-bold text-xl text-gray-800 tracking-tight">SIAKAD<span class="text-blue-600">PRO</span></span>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>

        {{-- DASHBOARD LINK (DYNAMIC ROUTE BASED ON ROLE) --}}
        @php
            $dashboardRoute = match(auth()->user()->role) {
                'admin' => 'admin.dashboard',
                'teacher' => 'guru.dashboard',
                'parent' => 'ortu.dashboard',
                'student' => 'siswa.dashboard',
                default => 'dashboard'
            };
        @endphp

        <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('*.dashboard') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
        </a>

        {{-- MENU KHUSUS GURU --}}
        @if(auth()->user()->role === 'teacher')
            <a href="{{ route('guru.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('guru.jadwal.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Jadwal Mengajar
            </a>
            <a href="{{ route('guru.nilai.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('guru.nilai.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Input Nilai
            </a>
        @endif

        {{-- MENU KHUSUS ORTU (FIX ALERT LOGIC) --}}
        @if(auth()->user()->role === 'parent')
            @php
                $unpaidCount = 0;
                $myStudent = \App\Models\Student::where('parent_user_id', auth()->id())->first();

                if($myStudent) {
                    // FIX: HANYA HITUNG YANG BELUM LUNAS (unpaid) DAN SUDAH DITERBITKAN (is_published = 1)
                    $unpaidCount = \App\Models\SppPayment::where('student_id', $myStudent->id)
                                    ->where('status', 'unpaid')
                                    ->where('is_published', 1)
                                    ->count();
                }
            @endphp

            <a href="{{ route('ortu.tagihan.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg transition {{ request()->routeIs('ortu.tagihan.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Pembayaran SPP
                </div>

                @if($unpaidCount > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                        {{ $unpaidCount }}
                    </span>
                @endif
            </a>
        @endif

        {{-- MENU KHUSUS SISWA --}}
        @if(auth()->user()->role === 'student')
            <a href="{{ route('siswa.jadwal') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('siswa.jadwal') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Jadwal Pelajaran
            </a>
        @endif

    </div>

    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar Aplikasi
            </button>
        </form>
    </div>
</nav>
