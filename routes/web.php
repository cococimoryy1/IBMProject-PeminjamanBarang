<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\RiwayatPeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\CategoryController;
Use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Artisan;

Route::get('/migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return "Migrasi berhasil dijalankan ✅";
    } catch (\Exception $e) {
        return "Error migrasi: " . $e->getMessage();
    }
});

Route::get('/', function () {
    return view('landingpage');
})->name('landing');

// Hanya untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Dashboard dan Fitur Utama (dilindungi dengan middleware auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);

    // Barang Routes
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::get('/barang/search', [BarangController::class, 'search'])->name('barang.search');
    Route::get('/barang/filter-low-stock', [BarangController::class, 'filterLowStock'])->name('barang.filterLowStock');
    Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');

    // Peminjaman Routes
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');

    // Pengembalian Routes
    Route::get('/peminjaman/return', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::patch('/peminjaman/{id}/return', [PengembalianController::class, 'returnItem'])->name('pengembalian.returnItem');

    // Riwayat Peminjaman Routes
    Route::get('/peminjaman/riwayat', [RiwayatPeminjamanController::class, 'index'])->name('peminjaman.riwayat');
    Route::get('/history/export/pdf', [RiwayatPeminjamanController::class, 'exportPdf'])->name('history.export.pdf');
});
