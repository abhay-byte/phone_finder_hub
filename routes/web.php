<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\PhoneController::class, 'index'])->name('home');
Route::get('/rankings', [\App\Http\Controllers\PhoneController::class, 'rankings'])->name('phones.rankings');
Route::get('/methodology/ueps', [\App\Http\Controllers\PhoneController::class, 'methodology'])->name('ueps.methodology');
Route::get('/methodology/fpi', [\App\Http\Controllers\PhoneController::class, 'fpiMethodology'])->name('fpi.methodology');
Route::resource('phones', \App\Http\Controllers\PhoneController::class)->only(['index', 'show']);
