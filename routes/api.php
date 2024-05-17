<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TempatTimbulanSampahController;
use App\Http\Controllers\UserController;


Route::prefix('auth')->group(function () {
    Route::post('/register/{user_role_id}/{tts_kategori_id}', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/status', [AuthController::class, 'status']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
});
Route::prefix('user')->group(function () {
    Route::prefix('role')->group(function () {
        Route::get('/list', [UserController::class, 'getUserRoleList']);
    });
    Route::prefix('profile')->group(function () {
        Route::post('/', [UserController::class, 'createUser']);
        Route::get('/list', [UserController::class, 'getUserList']);
        Route::get('/{id}', [UserController::class, 'getUserDetail']);
        Route::put('/{id}', [UserController::class, 'updateUser']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
});
Route::prefix('tempat-timbulan-sampah')->group(function () {
    Route::get('/kategori/list', [TempatTimbulanSampahController::class, 'getTempatTimbulanSampahKategoriList']);
    Route::get('/sektor/list', [TempatTimbulanSampahController::class, 'getTempatTimbulanSampahSektorList']);
    Route::get('/list', [TempatTimbulanSampahController::class, 'getTempatTimbulanSampahList']);
});