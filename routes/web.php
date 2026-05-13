<?php

use App\Http\Controllers\Admin\ImportReferensiKendaraanController;
use App\Http\Controllers\Admin\KendaraanExportController;
use App\Http\Controllers\Admin\OpdController;
use App\Http\Controllers\Admin\PeminjamanBpkbAdminController;
use App\Http\Controllers\Admin\UserOpdController;
use App\Http\Controllers\Admin\VerifikasiKendaraanController;
use App\Http\Controllers\Admin\VerifikasiMutasiKendaraanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\MutasiKendaraanController;
use App\Http\Controllers\PeminjamanBpkbController;
use App\Http\Controllers\ReminderPajakController;
use App\Http\Controllers\RiwayatPlatNomorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/reminder-pajak', ReminderPajakController::class)->name('reminder-pajak.index');

    Route::get('/referensi-kendaraans/search', [KendaraanController::class, 'searchReferensi'])->name('referensi-kendaraans.search');
    Route::post('/kendaraans/{kendaraan}/submit', [KendaraanController::class, 'submit'])->name('kendaraans.submit');
    Route::post('/kendaraans/{kendaraan}/riwayat-plat-nomor', [RiwayatPlatNomorController::class, 'store'])->name('kendaraans.riwayat-plat.store');
    Route::delete('/riwayat-plat-nomor/{riwayatPlatNomor}', [RiwayatPlatNomorController::class, 'destroy'])->name('kendaraans.riwayat-plat.destroy');
    Route::resource('kendaraans', KendaraanController::class);

    Route::middleware('role:user_opd')->group(function (): void {
        Route::resource('mutasi-kendaraans', MutasiKendaraanController::class)
            ->only(['index', 'create', 'store', 'destroy'])
            ->parameters(['mutasi-kendaraans' => 'mutasiKendaraan']);
        Route::resource('peminjaman-bpkbs', PeminjamanBpkbController::class)
            ->only(['index', 'create', 'store', 'destroy'])
            ->parameters(['peminjaman-bpkbs' => 'peminjamanBpkb']);
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/peminjaman-bpkb', [PeminjamanBpkbAdminController::class, 'index'])->name('peminjaman-bpkbs.index');
        Route::patch('/peminjaman-bpkb/{peminjamanBpkb}', [PeminjamanBpkbAdminController::class, 'update'])->name('peminjaman-bpkbs.update');
        Route::get('/kendaraans/export', KendaraanExportController::class)->name('kendaraans.export');
        Route::get('/import-database', [ImportReferensiKendaraanController::class, 'index'])->name('import-referensi.index');
        Route::get('/import-database/template', [ImportReferensiKendaraanController::class, 'template'])->name('import-referensi.template');
        Route::post('/import-database', [ImportReferensiKendaraanController::class, 'store'])->name('import-referensi.store');
        Route::get('/import-database/{referensiKendaraan}/edit', [ImportReferensiKendaraanController::class, 'edit'])->name('import-referensi.edit');
        Route::put('/import-database/{referensiKendaraan}', [ImportReferensiKendaraanController::class, 'update'])->name('import-referensi.update');
        Route::delete('/import-database/{referensiKendaraan}', [ImportReferensiKendaraanController::class, 'destroy'])->name('import-referensi.destroy');
        Route::get('/verifikasi-kendaraan', [VerifikasiKendaraanController::class, 'index'])->name('verifikasi.index');
        Route::patch('/verifikasi-kendaraan/{kendaraan}', [VerifikasiKendaraanController::class, 'update'])->name('verifikasi.update');
        Route::patch('/verifikasi-mutasi-kendaraan/{mutasiKendaraan}', [VerifikasiMutasiKendaraanController::class, 'update'])->name('verifikasi-mutasi.update');
        Route::resource('opds', OpdController::class);
        Route::resource('users', UserOpdController::class)->parameters(['users' => 'userOpd']);
    });
});
