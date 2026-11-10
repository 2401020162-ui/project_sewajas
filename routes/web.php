<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\TestimoniController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::get('/', [KatalogController::class, 'beranda'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'katalog'])->name('katalog');
Route::get('/jas/{jas}', [KatalogController::class, 'detail'])->name('jas.detail');

/*
|--------------------------------------------------------------------------
| Autentikasi (customer & admin memakai form yang sama)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'form'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.proses');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.proses');
});

Route::post('/keluar', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Area customer (wajib login & role customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'customer'])->group(function () {
    // Keranjang
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::patch('/keranjang/{keranjang}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{keranjang}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');
    Route::delete('/keranjang', [KeranjangController::class, 'kosongkan'])->name('keranjang.kosongkan');
    Route::post('/checkout', [KeranjangController::class, 'checkout'])->name('checkout');

    // Pesanan
    Route::get('/pesanan-saya', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan-saya/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan-saya/{pesanan}/batal', [PesananController::class, 'batal'])->name('pesanan.batal');

    // Pembayaran
    Route::get('/pembayaran/{pesanan}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{pesanan}', [PembayaranController::class, 'store'])->name('pembayaran.store');

    // Testimoni
    Route::post('/testimoni', [TestimoniController::class, 'store'])->name('testimoni.store');
});

/*
|--------------------------------------------------------------------------
| Area admin (wajib login & role admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('jas', Admin\JasController::class)
        ->except('show')
        ->parameters(['jas' => 'jas']);
    Route::resource('kategori', Admin\KategoriController::class)->only(['index', 'store', 'update', 'destroy']);

    // Verifikasi pembayaran
    Route::get('/verifikasi', [Admin\VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi/{pembayaran}/setujui', [Admin\VerifikasiController::class, 'setujui'])->name('verifikasi.setujui');
    Route::post('/verifikasi/{pembayaran}/tolak', [Admin\VerifikasiController::class, 'tolak'])->name('verifikasi.tolak');

    // Pesanan
    Route::get('/pesanan', [Admin\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [Admin\PesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan/{pesanan}/selesai', [Admin\PesananController::class, 'selesai'])->name('pesanan.selesai');
    Route::post('/pesanan/{pesanan}/batal', [Admin\PesananController::class, 'batal'])->name('pesanan.batal');

    // Pengguna
    Route::get('/pengguna', [Admin\PenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/{user}', [Admin\PenggunaController::class, 'show'])->name('pengguna.show');

    // Laporan
    Route::get('/laporan', [Admin\LaporanController::class, 'index'])->name('laporan');
});
