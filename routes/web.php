<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\WarungController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;

// Root langsung ke Login
Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get( '/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
});

// Protected routes
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kasir
    Route::get( '/kasir',        [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/proses', [KasirController::class, 'proses'])->name('kasir.proses');

    // Penjualan
    Route::get('/penjualan',      [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/{id}', [PenjualanController::class, 'detail'])->name('penjualan.detail');

    // Laporan (semua role bisa akses)
    Route::get('/laporan',        [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // Profile (semua role)
    Route::get( '/profile',         [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/nama',    [\App\Http\Controllers\ProfileController::class, 'updateNama'])->name('profile.nama');
    Route::post('/profile/username',[\App\Http\Controllers\ProfileController::class, 'updateUsername'])->name('profile.username');
    Route::post('/profile/password',[\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Hanya pemilik
    Route::middleware('pemilik')->group(function () {

        // Barang: seluruh operasi (lihat, tambah, edit, hapus, stok) hanya pemilik
        Route::get   ('/barang',            [BarangController::class, 'index'])->name('barang.index');
        Route::get   ('/barang/{id}/edit',  [BarangController::class, 'edit'])->name('barang.edit');
        Route::put   ('/barang/{id}',       [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{id}',       [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::get( '/barang/tambah-stok',  [BarangController::class, 'showTambahStok'])->name('barang.tambah-stok');
        Route::post('/barang/tambah-stok',  [BarangController::class, 'prosesStok'])->name('barang.proses-stok');
        Route::get( '/barang/tambah',       [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang',              [BarangController::class, 'store'])->name('barang.store');

        // Kategori semua operasi hanya pemilik
        Route::get   ('/kategori',           [KategoriController::class, 'index'])->name('kategori.index');
        Route::post  ('/kategori',           [KategoriController::class, 'index'])->name('kategori.store');
        Route::get   ('/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put   ('/kategori/{id}',      [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{id}',      [KategoriController::class, 'destroy'])->name('kategori.destroy');

        Route::get   ('/pegawai',            [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::post  ('/pegawai',            [PegawaiController::class, 'index'])->name('pegawai.store');
        Route::get   ('/pegawai/{id}/edit',  [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put   ('/pegawai/{id}',       [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/{id}',       [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

        Route::get   ('/warung',             [WarungController::class, 'index'])->name('warung.index');
        Route::post  ('/warung',             [WarungController::class, 'index'])->name('warung.store');
        Route::get   ('/warung/{id}/edit',   [WarungController::class, 'edit'])->name('warung.edit');
        Route::put   ('/warung/{id}',        [WarungController::class, 'update'])->name('warung.update');
        Route::delete('/warung/{id}',        [WarungController::class, 'destroy'])->name('warung.destroy');
    });
});
