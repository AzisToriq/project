<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Proses Autentikasi (Cek Email & Password)
        $request->authenticate();

        // 2. Regenerate Session ID (Standar Keamanan Laravel)
        $request->session()->regenerate();

        // 3. REDIRECT LANGSUNG KE 'dashboard'
        // Logika tampilan (Admin/Guru/Siswa) sudah dihandle oleh DashboardController@index
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Simpan status role sebelum logout agar sistem tahu siapa yang keluar
        $user = Auth::user();
        $isSiswa = ($user && $user->role === 'siswa');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // JIKA YANG LOGOUT SISWA -> Kembalikan ke Login Khusus Siswa
        if ($isSiswa) {
            return redirect()->route('siswa.login');
        }

        // JIKA GURU/ADMIN -> Kembalikan ke Login Utama
        return redirect('/');
    }
}
