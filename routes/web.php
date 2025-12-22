<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\WargaController;
use App\http\Controllers\HomeController;

// HOME
Route::get('/utama/home', [HomeController::class, 'index'])->name('home');

// Halaman Register
Route::get('/auth/register', function () {
    return view('auth.register');
});
Route::post('/auth/register', [AuthController::class, 'register'])->name('register');

// ===============================
// AUTH
// ===============================

// Halaman Login Warga
Route::get('/auth/login', [AuthController::class, 'showLoginWarga'])->name('login.warga');
Route::post('/auth/login', [AuthController::class, 'loginWarga'])->name('login.warga.submit');

// Halaman Login Petugas/Admin
Route::get('/auth/login-staff', [AuthController::class, 'showLoginPetugasAdmin'])->name('login.staff');
Route::post('/auth/login-staff', [AuthController::class, 'loginPetugasAdmin'])->name('login.staff.submit');

// Logout
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');


// ===============================
// DASHBOARD PER ROLE
// ===============================

// ✅ Admin area (hanya bisa diakses role admin)
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // [UPDATE] Store user berdasarkan role dari URL (petugas/warga)
    Route::post('/user/store/{role}', [AdminController::class, 'storeByRole'])
        ->whereIn('role', ['petugas', 'warga'])
        ->name('admin.user.store');

    Route::put('/user/update/{id}', [AdminController::class, 'update'])->name('admin.user.update');
    Route::delete('/user/delete/{id}', [AdminController::class, 'destroy'])->name('admin.user.delete');

    Route::post('/tps/store', [AdminController::class, 'storeTps'])->name('admin.tps.store');
    Route::put('/tps/update/{id}', [AdminController::class, 'updateTps'])->name('admin.tps.update');
    Route::delete('/tps/delete/{id}', [AdminController::class, 'destroyTps'])->name('admin.tps.delete');

    Route::post('/mobil/store', [AdminController::class, 'storeMobil'])->name('admin.mobil.store');
    Route::put('/mobil/update/{id}', [AdminController::class, 'updateMobil'])->name('admin.mobil.update');
    Route::delete('/mobil/delete/{id}', [AdminController::class, 'destroyMobil'])->name('admin.mobil.delete');

    Route::get('/laporan', [AdminController::class, 'laporan'])->name('admin.laporanhistoris');
});

// ✅ Petugas area (hanya bisa diakses role petugas)
Route::get('/petugas/dashboard', [PetugasController::class, 'dashboard'])
    ->middleware('checklogin')
    ->name('petugas.dashboard');

// ✅ Warga area (hanya bisa diakses role warga)
Route::get('/warga/dashboard', [WargaController::class, 'dashboard'])
    ->name('dashboard.warga');

Route::get('/warga/edukasi-sampah', [WargaController::class, 'edukasiSampah'])
        ->name('warga.edukasi');
