<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KehadiranVerificationController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MahasiswaFeaturesController;
use App\Http\Controllers\OrmawaController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RekapAnggotaController;
use App\Http\Controllers\WadirReportController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index']);

    // Admin Group
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::resource('pengguna', PenggunaController::class);
        Route::resource('ormawa', OrmawaController::class);
        Route::resource('mahasiswa', MahasiswaController::class);
    });

    // Pengurus Ormawa Group
    Route::middleware('role:pengurus_ormawa')->prefix('pengurus')->name('pengurus.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pengurusDashboard'])->name('dashboard');
        Route::resource('kegiatan', KegiatanController::class);
        
        // Attendance Verification
        Route::get('/kehadiran', [KehadiranVerificationController::class, 'index'])->name('kehadiran.index');
        Route::post('/kehadiran/{kehadiran}/approve', [KehadiranVerificationController::class, 'approve'])->name('kehadiran.approve');
        Route::post('/kehadiran/{kehadiran}/reject', [KehadiranVerificationController::class, 'reject'])->name('kehadiran.reject');
        
        // Member Recap
        Route::get('/rekap-keaktifan', [RekapAnggotaController::class, 'index'])->name('rekap.index');
    });

    // Mahasiswa KIP Group
    Route::middleware('role:mahasiswa_kip')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mahasiswaDashboard'])->name('dashboard');
        Route::get('/kegiatan', [MahasiswaFeaturesController::class, 'kegiatanIndex'])->name('kegiatan.index');
        Route::get('/kehadiran/catat/{kegiatan}', [MahasiswaFeaturesController::class, 'showCatatForm'])->name('kehadiran.catat.form');
        Route::post('/kehadiran/catat/{kegiatan}', [MahasiswaFeaturesController::class, 'storeKehadiran'])->name('kehadiran.catat.store');
        Route::get('/riwayat', [MahasiswaFeaturesController::class, 'riwayatIndex'])->name('riwayat');
        Route::get('/rekap', [MahasiswaFeaturesController::class, 'rekapDiri'])->name('rekap');
    });

    // Wadir/Pihak Kampus Group
    Route::middleware('role:wadir')->prefix('wadir')->name('wadir.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'wadirDashboard'])->name('dashboard');
        Route::get('/export-pdf', [WadirReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export-excel', [WadirReportController::class, 'exportExcel'])->name('export.excel');
    });
});
