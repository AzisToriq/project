<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthOrtuController extends Controller
{
    // 1. Tampilkan Halaman Login Khusus Ortu
    public function showLoginForm()
    {
        return view('auth.ortu-login'); // Nanti kita bikin view ini
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tambahkan syarat: Role harus 'parent' & Status Aktif
        $credentials['role'] = 'parent';
        $credentials['is_active'] = 1;

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('ortu.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah, atau akun bukan akun Orang Tua.',
        ])->onlyInput('email');
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('ortu.login');
    }
}
