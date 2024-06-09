<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SampahMasukController;
use App\Http\Controllers\SampahDiolahController;
use App\Http\Controllers\SampahDimanfaatkanController;
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
    Route::get('/kategori/list', [SampahMasukController::class, 'getSampahKategoriList']);
    Route::prefix('masuk')->group(function () {
        Route::post('/', [SampahMasukController::class, 'storeSampahMasuk']);
        Route::get('/status', [SampahMasukController::class, 'getSampahMasukStatus']);
        Route::get('/list', [SampahMasukController::class, 'getSampahMasukList']);
        Route::get('/{id}', [SampahMasukController::class, 'getSampahMasukDetail']);
        Route::put('/{id}', [SampahMasukController::class, 'updateSampahMasuk']);
    });
    Route::prefix('diolah')->group(function () {
        Route::post('/', [SampahDiolahController::class, 'storeSampahDiolah']);
        Route::get('/status', [SampahDiolahController::class, 'getSampahSampahDiolahControllerDiolahStatus']);
        Route::get('/list', [SampahDiolahController::class, 'getSampahDiolahList']);
        Route::get('/{id}', [SampahDiolahController::class, 'getSampahDiolahDetail']);
        Route::put('/{id}', [SampahDiolahController::class, 'updateSampahDiolah']);
    });
    Route::prefix('dimanfaatkan')->group(function () {
        Route::post('/', [SampahDimanfaatkanController::class, 'storeSampahDimanfaatkan']);
        Route::get('/list', [SampahDimanfaatkanController::class, 'getSampahDimanfaatkanList']);
        Route::get('/{id}', [SampahDimanfaatkanController::class, 'getSampahDimanfaatkanDetail']);
        Route::put('/{id}', [SampahDimanfaatkanController::class, 'updateSampahDimanfaatkan']);
        Route::post('/{id}/distribusi', [SampahDimanfaatkanController::class, 'storeDistribusiSampahDimanfaatkan']);
        Route::get('/{id}/distribusi/list', [SampahDimanfaatkanController::class, 'getDistribusiSampahDimanfaatkanList']);
    });
});