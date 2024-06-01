<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SampahController;
use App\Http\Controllers\TempatTimbulanSampahController;
use App\Http\Controllers\UserController;


Route::prefix('auth')->group(function () {
    Route::post('/register/tempat-timbulan-sampah', [AuthController::class, 'registerTempatTimbulanSampah']);
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
        Route::delete('/{id}', [UserController::class, 'deleteUser']);
    });
});
Route::prefix('tempat-timbulan-sampah')->group(function () {
    Route::get('/kategori/list', [TempatTimbulanSampahController::class, 'getTempatTimbulanSampahKategoriList']);
    Route::get('/sektor/list', [TempatTimbulanSampahController::class, 'getTempatTimbulanSampahSektorList']);
    Route::get('/list', [TempatTimbulanSampahController::class, 'getTempatTimbulanSampahList']);
    Route::get('/{id}', [TempatTimbulanSampahController::class, 'getTempatTimbulanSampahDetail']);
    Route::put('/{id}', [TempatTimbulanSampahController::class, 'updateTempatTimbulanSampah']);
});
Route::prefix('sampah')->group(function () {
    Route::get('/kategori/list', [SampahController::class, 'getSampahKategoriList']);
    Route::prefix('masuk')->group(function () {
        Route::post('/', [SampahController::class, 'storeSampahMasuk']);
        Route::get('/status', [SampahController::class, 'getSampahMasukStatus']);
        Route::get('/list', [SampahController::class, 'getSampahMasukList']);
        Route::get('/{id}', [SampahController::class, 'getSampahMasukDetail']);
        Route::put('/{id}', [SampahController::class, 'updateSampahMasuk']);
    });
    Route::prefix('diolah')->group(function () {
        Route::post('/', [SampahController::class, 'storeSampahDiolah']);
        Route::get('/status', [SampahController::class, 'getSampahDiolahStatus']);
        Route::get('/list', [SampahController::class, 'getSampahDiolahList']);
        Route::get('/{id}', [SampahController::class, 'getSampahDiolahDetail']);
        Route::put('/{id}', [SampahController::class, 'updateSampahDiolah']);
    });
});