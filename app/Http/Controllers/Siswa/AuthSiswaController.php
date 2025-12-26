<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthSiswaController extends Controller
{
    public function login()
    {
        return view('auth.siswa-login');
    }

    public function authenticate(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'username' => ['required'], // Input name di form tetap 'username'
            'password' => ['required'],
        ]);

        // 2. DETEKSI TIPE LOGIN (Email atau NIS?)
        // Cek apakah input yang dimasukkan formatnya email valid?
        // Kalau format email -> kita cek ke kolom 'email'
        // Kalau bukan email -> kita cek ke kolom 'username' (NIS)
        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Eksekusi Login
        if (Auth::attempt([
            $loginType => $request->username, // Dynamic Key: 'email' atau 'username'
            'password' => $request->password,
            'role'     => 'student', // Wajib role student
            'is_active'=> 1          // Wajib aktif
        ], $request->boolean('remember'))) {

            // SUKSES
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // 4. GAGAL
        return back()->withErrors([
            'username' => 'NIS/Email atau password salah.',
        ])->onlyInput('username');
    }

    // Tambahkan fungsi logout biar lengkap
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('siswa.login');
    }
}
