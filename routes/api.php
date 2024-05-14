<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TempatTimbulanSampahController;
use App\Http\Controllers\UserController;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/status', [AuthController::class, 'status']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/change-password/{id}', [AuthController::class, 'changePassword']);
});
Route::prefix('user')->group(function () {
    Route::get('/profile/list', [UserController::class, 'list']);
    Route::get('/profile/{id}', [UserController::class, 'show']);
    Route::post('/profile', [UserController::class, 'store']);
    Route::put('/profile/{id}', [UserController::class, 'update']);
    Route::delete('/profile/{id}', [UserController::class, 'destroy']);
});
Route::prefix('tempat-timbulan-sampah')->group(function () {
    Route::get('/kategori/list', [TempatTimbulanSampahController::class, 'listTempatTimbulanSampahKategori']);
    Route::get('/sektor/list', [TempatTimbulanSampahController::class, 'listTempatTimbulanSampahSektor']);
    Route::get('/list', [TempatTimbulanSampahController::class, 'list']);
});