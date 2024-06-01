<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('It Works!');
});
Route::get('/artisan/storage-link', function () {
    $exitCode = Artisan::call('storage:link');
    return 'Storage link created';
});
