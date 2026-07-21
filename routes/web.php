<?php

use App\Http\Controllers\AnggotaRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KehadiranVerificationController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MahasiswaFeaturesController;
use App\Http\Controllers\OrmawaController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapAnggotaController;
use App\Http\Controllers\WadirReportController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// AJAX Route for Jurusan/Prodi
Route::get('/api/prodi', function (\Illuminate\Http\Request $request) {
    $jurusanName = $request->query('jurusan');
    $jurusan = \App\Models\Jurusan::where('nama', $jurusanName)->first();
    if ($jurusan) {
        return response()->json($jurusan->prodis()->pluck('nama'));
    }
    return response()->json([]);
})->name('api.prodi');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index']);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin Group
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::resource('pengguna', PenggunaController::class);
        Route::resource('ormawa', OrmawaController::class);
        Route::resource('mahasiswa', MahasiswaController::class);

        // Audit Logs (Admin)
        Route::get('/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');

        // Approve / Reject Pending Pengurus
        Route::post('/pengguna/{pengguna}/approve', [PenggunaController::class, 'approve'])->name('pengguna.approve');
        Route::post('/pengguna/{pengguna}/reject', [PenggunaController::class, 'reject'])->name('pengguna.reject');

        // Export Laporan (Shared WadirController)
        Route::get('/export-pdf', [App\Http\Controllers\WadirReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export-excel', [App\Http\Controllers\WadirReportController::class, 'exportExcel'])->name('export.excel');
    });

    // Pengurus Ormawa Group
    Route::middleware('role:pengurus_ormawa')->prefix('pengurus')->name('pengurus.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pengurusDashboard'])->name('dashboard');
        Route::post('/ormawa/toggle-recruitment', [DashboardController::class, 'toggleRecruitment'])->name('ormawa.toggle-recruitment');
        Route::patch('/kegiatan/{kegiatan}/toggle-absensi', [KegiatanController::class, 'toggleAbsensi'])->name('kegiatan.toggle-absensi');
        Route::resource('kegiatan', KegiatanController::class);

        // Attendance Verification
        Route::get('/kehadiran', [KehadiranVerificationController::class, 'index'])->name('kehadiran.index');
        Route::post('/kehadiran/bulk-approve', [KehadiranVerificationController::class, 'bulkApprove'])->name('kehadiran.bulk-approve');
        Route::post('/kehadiran/{kehadiran}/approve', [KehadiranVerificationController::class, 'approve'])->name('kehadiran.approve');
        Route::post('/kehadiran/{kehadiran}/reject', [KehadiranVerificationController::class, 'reject'])->name('kehadiran.reject');

        // Member Recap
        Route::get('/rekap-keaktifan', [RekapAnggotaController::class, 'index'])->name('rekap.index');

        // Anggota Management (Pengurus memverifikasi permintaan)
        Route::get('/anggota', [AnggotaRequestController::class, 'index'])->name('anggota.index');
        Route::post('/anggota/{anggotaRequest}/approve', [AnggotaRequestController::class, 'approve'])->name('anggota.approve');
        Route::post('/anggota/{anggotaRequest}/reject', [AnggotaRequestController::class, 'reject'])->name('anggota.reject');
        Route::post('/anggota/{mahasiswa}/hapus', [AnggotaRequestController::class, 'removeMember'])->name('anggota.hapus');

        // Pengumuman Management
        Route::resource('pengumuman', \App\Http\Controllers\PengumumanController::class);
    });

    // Mahasiswa KIP Group
    Route::middleware('role:mahasiswa_kip')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mahasiswaDashboard'])->name('dashboard');
        
        // Pendaftaran Ormawa
        Route::get('/pendaftaran', [MahasiswaFeaturesController::class, 'pendaftaranIndex'])->name('pendaftaran.index');
        Route::post('/pendaftaran', [MahasiswaFeaturesController::class, 'daftarOrmawa'])->name('pendaftaran.store');

        Route::get('/kegiatan', [MahasiswaFeaturesController::class, 'kegiatanIndex'])->name('kegiatan.index');
        Route::get('/kegiatan/{kegiatan}/catat', [MahasiswaFeaturesController::class, 'showCatatForm'])->name('kehadiran.catat');
        Route::post('/kegiatan/{kegiatan}/catat', [MahasiswaFeaturesController::class, 'storeKehadiran'])->name('kehadiran.catat.store');
        Route::get('/riwayat', [MahasiswaFeaturesController::class, 'riwayatIndex'])->name('riwayat');
        Route::get('/rekap', [MahasiswaFeaturesController::class, 'rekapDiri'])->name('rekap');
        Route::get('/timeline', [MahasiswaFeaturesController::class, 'timeline'])->name('timeline');
    });

    // Wadir/Pihak Kampus Group
    Route::middleware('role:wadir')->prefix('wadir')->name('wadir.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'wadirDashboard'])->name('dashboard');
        Route::get('/export-pdf', [WadirReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export-excel', [WadirReportController::class, 'exportExcel'])->name('export.excel');
    });
});
