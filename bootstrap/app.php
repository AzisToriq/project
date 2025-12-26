<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth; // Tambahan penting buat cek user
use Illuminate\Http\Request;         // Tambahan penting buat cek request URL

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ==========================================================
        // 1. DAFTAR ALIAS MIDDLEWARE
        // ==========================================================
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // ==========================================================
        // 2. REDIRECT USER YANG SUDAH LOGIN (GUEST -> DASHBOARD)
        // ==========================================================
        // Logic: Kalau user iseng buka halaman login padahal sudah login
        $middleware->redirectUsersTo(function (Request $request) {
            // KARENA KITA SUDAH PAKAI 'SMART DASHBOARD CONTROLLER',
            // SEMUA USER DILEMPAR KE SATU PINTU SAJA:
            return route('dashboard');
        });

        // ==========================================================
        // 3. REDIRECT TAMU / BELUM LOGIN (AUTH -> LOGIN PAGE)
        // ==========================================================
        // Logic: Kalau tamu maksa buka halaman dashboard tanpa login
        $middleware->redirectGuestsTo(function (Request $request) {

            // Kalau URL mengandung kata 'ortu', arahkan ke login ortu
            if ($request->is('ortu/*')) {
                return route('ortu.login');
            }

            // Kalau URL mengandung kata 'siswa', arahkan ke login siswa
            if ($request->is('siswa/*')) {
                return route('siswa.login');
            }

            // Default ke login biasa (Guru/Admin)
            return route('login');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
