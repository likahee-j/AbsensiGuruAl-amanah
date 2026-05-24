<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HariLiburController;
use App\Http\Controllers\Admin\KelolaAbsensiController;
use App\Http\Controllers\Admin\KioskController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\QrController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TahunPelajaranController;
use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Guru\RiwayatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return in_array(Auth::user()?->role, ['admin', 'kepsek'], true)
        ? redirect()->route('admin.dashboard')
        : redirect()->route('absensi.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/account', [ProfileController::class, 'updateAccount'])->name('profile.account');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:guru,kepsek'])->group(function () {
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkin'])->name('absensi.checkin');
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkout'])->name('absensi.checkout');
    Route::post('/absensi/simulate', [AbsensiController::class, 'simulate'])->name('absensi.simulate');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

Route::middleware(['auth', 'role:admin,kepsek'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Data - Pengguna (Admin / Kepsek / Guru)
    Route::prefix('pengguna/{role}')->name('pengguna.')
        ->where(['role' => 'admin|kepsek|guru'])
        ->group(function () {
            Route::get('/', [PenggunaController::class, 'index'])->name('index');
            Route::get('/create', [PenggunaController::class, 'create'])->name('create');
            Route::post('/', [PenggunaController::class, 'store'])->name('store');
            Route::get('/{user}', [PenggunaController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [PenggunaController::class, 'edit'])->name('edit');
            Route::put('/{user}', [PenggunaController::class, 'update'])->name('update');
            Route::delete('/{user}', [PenggunaController::class, 'destroy'])->name('destroy');
            Route::get('/{user}/qr', [PenggunaController::class, 'qr'])->name('qr');
        });

    // Master Data - Administrasi
    Route::get('/sekolah', [SettingsController::class, 'edit'])->name('sekolah.edit');
    Route::put('/sekolah', [SettingsController::class, 'update'])->name('sekolah.update');
    Route::post('/sekolah/logo', [SettingsController::class, 'updateLogo'])->name('sekolah.logo');

    Route::get('/tapel', [TahunPelajaranController::class, 'index'])->name('tapel.index');
    Route::post('/tapel', [TahunPelajaranController::class, 'store'])->name('tapel.store');
    Route::put('/tapel/{tapel}', [TahunPelajaranController::class, 'update'])->name('tapel.update');
    Route::delete('/tapel/{tapel}', [TahunPelajaranController::class, 'destroy'])->name('tapel.destroy');

    Route::get('/libur', [HariLiburController::class, 'index'])->name('libur.index');
    Route::post('/libur', [HariLiburController::class, 'store'])->name('libur.store');
    Route::put('/libur/{libur}', [HariLiburController::class, 'update'])->name('libur.update');
    Route::delete('/libur/{libur}', [HariLiburController::class, 'destroy'])->name('libur.destroy');

    // Absensi
    Route::get('/absensi', [KelolaAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/cell', [KelolaAbsensiController::class, 'updateCell'])->name('absensi.cell');

    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/print', [RekapController::class, 'print'])->name('rekap.print');
    Route::get('/rekap/export/excel', [RekapController::class, 'exportExcel'])->name('rekap.export.excel');
    Route::get('/rekap/export/pdf', [RekapController::class, 'exportPdf'])->name('rekap.export.pdf');

    Route::get('/qr', [QrController::class, 'index'])->name('qr.index');
    Route::get('/qr/generate', [QrController::class, 'generate'])->name('qr.generate');

    // Mode Kios: operator (admin/kepsek) scan QR identitas guru/kepsek dari laptop sekolah
    Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
    Route::post('/kiosk/scan', [KioskController::class, 'scan'])->name('kiosk.scan');
});

Route::get('/api/qr/generate', [QrController::class, 'generate'])
    ->middleware(['auth', 'role:admin,kepsek'])
    ->name('api.qr.generate');

require __DIR__.'/auth.php';
