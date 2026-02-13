<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\NominalController;

use PHPUnit\Framework\Attributes\Group;


Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');
    Route::get('/register', [AuthController::class, 'register'])
    ->name('register');
    Route::post('/register', [AuthController::class, 'store'])
    ->name('register.process');
});

Route::middleware(['auth'])->group(function () {
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna');
Route::get('/pengguna/tambah', [PenggunaController::class, 'create'])->name('pengguna.tambah');
Route::get('/pengguna/{id_pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
Route::put('/pengguna/{id_pengguna}/update', [PenggunaController::class, 'update'])->name('pengguna.update');
Route::delete('/pengguna/{id_pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.hapus');
Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');

Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction');
Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
Route::put('/transaction/update/{id}', [TransactionController::class, 'update'])->name('transaction.update');
Route::delete('/transaction/delete/{id}', [TransactionController::class, 'destroy'])->name('transaction.destroy');


Route::get('/game', [GameController::class, 'index'])->name('game');
Route::post('/game/store', [GameController::class, 'store'])->name('game.store');
Route::put('/game/update/{id}', [GameController::class, 'update'])->name('game.update');
Route::delete('/game/delete/{id}', [GameController::class, 'destroy'])->name('game.delete');

Route::prefix('admin/nominal')->group(function () {
    Route::get('/', [NominalController::class, 'index'])->name('nominal');
    Route::post('/store', [NominalController::class, 'store'])->name('nominal.store');
    Route::put('/update/{id}', [NominalController::class, 'update'])->name('nominal.update');
    Route::delete('/delete/{id}', [NominalController::class, 'destroy'])->name('nominal.delete');
    Route::post('/fetch-digiflazz', [NominalController::class, 'fetchFromDigiflazz'])->name('nominal.fetchDigiflazz');
});

});
