<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// IMPORT CONTROLLERS
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
// Admin
use App\Http\Controllers\Admin\DataMasterController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SppController;
// Guru
use App\Http\Controllers\Guru\JadwalGuruController;
use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Guru\WaliKelasController;
use App\Http\Controllers\Guru\AssignmentController;
// Siswa
use App\Http\Controllers\Siswa\RaporController;
use App\Http\Controllers\Siswa\AuthSiswaController;
use App\Http\Controllers\Siswa\JadwalSiswaController;
use App\Http\Controllers\Siswa\TugasSiswaController;
// Ortu
use App\Http\Controllers\Ortu\AuthOrtuController;
use App\Http\Controllers\Ortu\SppOrtuController;
use App\Http\Controllers\Ortu\PerkembanganController;

/*
|--------------------------------------------------------------------------
| Web Routes (FINAL & COMPLETE - WITH ADVANCED FEATURES)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// ====================================================
// 1. GUEST (LOGIN PAGE)
// ====================================================
Route::middleware('guest')->group(function () {
    // Siswa
    Route::get('/siswa/login', [AuthSiswaController::class, 'login'])->name('siswa.login');
    Route::post('/siswa/login', [AuthSiswaController::class, 'authenticate'])->name('siswa.authenticate');

    // Ortu
    Route::get('/ortu/login', [AuthOrtuController::class, 'showLoginForm'])->name('ortu.login');
    Route::post('/ortu/login', [AuthOrtuController::class, 'login'])->name('ortu.authenticate');
});

require __DIR__.'/auth.php'; // Breeze Login (Admin/Guru)

// ====================================================
// 2. SHARED AUTH ROUTES (DASHBOARD & PROFIL)
// ====================================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ====================================================
// 3. ADMIN ROUTES
// ====================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('schedule', AdminScheduleController::class);

    // Grouping Controller SPP
    Route::controller(SppController::class)->prefix('spp')->name('spp.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/verifikasi', 'verification')->name('verification');
        Route::get('/arsip', 'archive')->name('archive');

        // --- GENERATE TAGIHAN (MASSAL & SATUAN) ---
        Route::post('/generate', 'store')->name('store'); // Massal
        Route::post('/store-individual', 'storeIndividual')->name('store_individual'); // Satuan (Baru)
        Route::get('/get-students/{classroom_id}', 'getStudentsByClass')->name('get_students'); // AJAX (Baru)

        // --- AKSI MASSAL ---
        Route::post('/publish-all', 'publishAll')->name('publish_all');
        Route::delete('/delete-all', 'deleteAll')->name('delete_all');

        // --- LOGIC DISPENSASI (APPROVAL & MANUAL) ---
        Route::post('/dispensation/{id}', 'saveDispensation')->name('dispensation'); // Admin Manual Input
        Route::post('/dispensation/{id}/approve', 'approveDispensation')->name('dispensation.approve'); // Approve
        Route::post('/dispensation/{id}/reject', 'rejectDispensation')->name('dispensation.reject');   // Reject

        // --- LOGIC PEMBAYARAN & VERIFIKASI ---
        Route::post('/{id}/verify', 'verify')->name('verify'); // Verifikasi Online
        Route::post('/{id}/reject', 'reject')->name('reject'); // Tolak Online
        Route::post('/{id}/pay-manual', 'payManual')->name('pay_manual'); // Bayar Tunai (Baru)
        Route::post('/{id}/cancel-payment', 'cancelPayment')->name('cancel_payment'); // Batal Bayar (Baru)

        // --- EDIT & HAPUS ---
        Route::post('/{id}/toggle', 'togglePublish')->name('toggle');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// ====================================================
// 4. DATA MASTER (ADMIN WRITE / GURU READ)
// ====================================================
Route::middleware(['auth'])->prefix('guru')->name('guru.data.')->group(function () {

    // Read Only
    Route::get('/mapel', [DataMasterController::class, 'indexSubjects'])->name('subjects.index');
    Route::get('/kelas', [DataMasterController::class, 'indexClassrooms'])->name('classrooms.index');
    Route::get('/siswa', [DataMasterController::class, 'indexStudents'])->name('students.index');
    Route::get('/siswa/kelas/{class_id}', [DataMasterController::class, 'showStudentsByClass'])->name('students.show_class');

    // Admin Full Access (CRUD)
    Route::middleware('role:admin')->group(function() {
        // Mapel
        Route::post('/mapel', [DataMasterController::class, 'storeSubject'])->name('subjects.store');
        Route::put('/mapel/{id}', [DataMasterController::class, 'updateSubject'])->name('subjects.update');
        Route::delete('/mapel/{id}', [DataMasterController::class, 'destroySubject'])->name('subjects.destroy');

        // Kelas
        Route::post('/kelas', [DataMasterController::class, 'storeClassroom'])->name('classrooms.store');
        Route::put('/kelas/{id}', [DataMasterController::class, 'updateClassroom'])->name('classrooms.update');
        Route::delete('/kelas/{id}', [DataMasterController::class, 'destroyClassroom'])->name('classrooms.destroy');
        Route::post('/kelas/{id}/toggle-rapor', [DataMasterController::class, 'toggleRaporStatus'])->name('classrooms.toggle_rapor');
        Route::get('/kelas/{class_id}/jadwal', [DataMasterController::class, 'previewJadwalKelas'])->name('classrooms.jadwal');

        // Siswa
        Route::post('/siswa', [DataMasterController::class, 'storeStudent'])->name('students.store');
        Route::put('/siswa/{id}', [DataMasterController::class, 'updateStudent'])->name('students.update');
        Route::delete('/siswa/{id}', [DataMasterController::class, 'destroyStudent'])->name('students.destroy');
        Route::get('/siswa/{id}/rapor-admin', [DataMasterController::class, 'showStudentRapor'])->name('students.rapor');

        // Guru
        Route::get('/guru', [DataMasterController::class, 'indexTeachers'])->name('teachers.index');
        Route::post('/guru', [DataMasterController::class, 'storeTeacher'])->name('teachers.store');
        Route::put('/guru/{id}', [DataMasterController::class, 'updateTeacher'])->name('teachers.update');
        Route::delete('/guru/{id}', [DataMasterController::class, 'destroyTeacher'])->name('teachers.destroy');

        // Plotting & Ortu
        Route::get('/plotting', [DataMasterController::class, 'indexAllocations'])->name('allocations.index');
        Route::post('/plotting', [DataMasterController::class, 'storeAllocation'])->name('allocations.store');
        Route::delete('/plotting/{id}', [DataMasterController::class, 'destroyAllocation'])->name('allocations.destroy');

        Route::get('/ortu', [DataMasterController::class, 'indexParents'])->name('parents.index');
        Route::get('/ortu/kelas/{class_id}', [DataMasterController::class, 'showParentsByClass'])->name('parents.show_class');
        Route::post('/ortu', [DataMasterController::class, 'storeParent'])->name('parents.store');
        Route::put('/ortu/{id}', [DataMasterController::class, 'updateParent'])->name('parents.update');
        Route::delete('/ortu/{id}', [DataMasterController::class, 'destroyParent'])->name('parents.destroy');
    });
});

// ====================================================
// 5. GURU ROUTES
// ====================================================
Route::middleware(['auth', 'role:teacher'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/jadwal-saya', [JadwalGuruController::class, 'index'])->name('jadwal.index');
    Route::resource('tugas', AssignmentController::class);
    Route::post('/tugas/submission/{submission_id}/grade', [AssignmentController::class, 'updateGrade'])->name('tugas.grade');

    Route::prefix('absensi')->name('absensi.')->controller(AbsensiController::class)->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/kelas/{classroom_id}', 'showMapel')->name('mapel');
        Route::get('/jurnal/{allocation_id}', 'jurnal')->name('jurnal');
        Route::get('/input/{allocation_id}', 'create')->name('input');
        Route::post('/store', 'store')->name('store');
        Route::delete('/delete/{allocation_id}/{date}', 'destroy')->name('destroy');
        Route::get('/rekap/{allocation_id}', 'rekap')->name('rekap');
    });

    Route::prefix('nilai')->name('nilai.')->controller(NilaiController::class)->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/input/{allocation_id}', 'create')->name('create');
        Route::post('/store/{allocation_id}', 'store')->name('store');
    });

    Route::controller(WaliKelasController::class)->group(function() {
        Route::get('/monitoring-kelas', 'index')->name('wali-kelas.index');
        Route::post('/rapor/toggle/{classroom_id}', 'toggleRapor')->name('rapor.toggle');
        Route::get('/rapor/show/{student_id}', 'show')->name('rapor.show');
    });
});

// ====================================================
// 6. SISWA ROUTES
// ====================================================
Route::middleware(['auth', 'role:student'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/jadwal', [JadwalSiswaController::class, 'index'])->name('jadwal');
    Route::get('/rapor', [RaporController::class, 'index'])->name('rapor');

    // TUGAS ROUTES
    Route::get('/tugas', [TugasSiswaController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{id}', [TugasSiswaController::class, 'show'])->name('tugas.show');

    // GUNAKAN POST MANUAL UNTUK SUBMIT AGAR ID TERBACA
    Route::post('/tugas/{id}/submit', [TugasSiswaController::class, 'store'])->name('tugas.submit');

    // UNTUK DELETE
    Route::delete('/tugas/{id}/delete', [TugasSiswaController::class, 'destroy'])->name('tugas.delete');
});

// ====================================================
// 7. ORTU ROUTES
// ====================================================
Route::middleware(['auth', 'role:parent'])->prefix('ortu')->name('ortu.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthOrtuController::class, 'logout'])->name('logout');
    Route::get('/perkembangan', [PerkembanganController::class, 'index'])->name('perkembangan.index');

    // Grouping SPP Ortu
    Route::controller(SppOrtuController::class)->group(function() {
        Route::get('/tagihan', 'index')->name('tagihan.index');
        Route::post('/tagihan/{id}/pay', 'uploadBukti')->name('tagihan.pay');
        Route::post('/spp/dispensasi/{id}', 'storeDispensation')->name('tagihan.dispensasi');
    });
});
