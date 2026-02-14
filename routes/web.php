<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\PhoneController::class, 'index'])->name('home');
Route::resource('phones', \App\Http\Controllers\PhoneController::class)->only(['index', 'show']);
