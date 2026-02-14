<?php

use Illuminate\Support\Facades\Route;
// Untuk web jika masih perlu
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\TransactionApiController;
use App\Http\Controllers\Api\GameApiController;
use App\Http\Controllers\Api\DigiflazzCallbackController;
use App\Http\Controllers\Api\ScraperGameApiController;
use App\Http\Controllers\Api\FFZCallbackController;

// Routes public
Route::post('/register', [AuthApiController::class, 'apiRegister']);
Route::post('/login', [AuthApiController::class, 'apiLogin']);

Route::get('games', [GameApiController::class, 'index']);
Route::get('games/{slug}', [GameApiController::class, 'show']);
Route::post('digiflazz/callback', [DigiflazzCallbackController::class, 'handle']);
Route::get('/gopay-games/two', [ScraperGameApiController::class, 'twoGames']);  
Route::get('/games/{game}/check-user', [ScraperGameApiController::class, 'checkUser']);

Route::post('/callback/ffz', [FFZCallbackController::class, 'handle'])
    ->name('callback.ffz');




// Routes yang perlu autentikasi
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('transactions', TransactionApiController::class)->only(['store', 'show']);
    Route::get('transactions/recent', [TransactionApiController::class, 'recent']);
    Route::post('transactions/{id}/process', [TransactionApiController::class, 'process']);
    Route::post('/logout', [AuthApiController::class, 'apiLogout']);  // Tambahkan logout
});