<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'It works!';
});
Route::get('/artisan/storage-link', function () {
    $exitCode = Artisan::call('storage:link');
    return 'Storage link created';
});
