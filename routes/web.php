<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialController;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::prefix('auth')->group(function () {
    Route::prefix('google')->group(function () {
        Route::get('redirect', [SocialController::class, 'googleRedirect'])->name('google.redirect');
        Route::get('callback', [SocialController::class, 'googleCallback'])->name('google.callback');
    });
});

Route::get('/{pathMatch}', function () {
    return view('index');
})->where('pathMatch', '.*');
