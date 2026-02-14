<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\PhoneController::class, 'index'])->name('home');
Route::get('/phones/search', [\App\Http\Controllers\PhoneController::class, 'search'])->name('phones.search');
Route::get('/rankings', [\App\Http\Controllers\PhoneController::class, 'rankings'])->name('phones.rankings');
Route::get('/methodology/ueps', [\App\Http\Controllers\PhoneController::class, 'methodology'])->name('ueps.methodology');
Route::get('/methodology/fpi', [\App\Http\Controllers\PhoneController::class, 'fpiMethodology'])->name('fpi.methodology');
Route::get('/compare', [\App\Http\Controllers\ComparisonController::class, 'index'])->name('phones.compare');
Route::get('/docs', [\App\Http\Controllers\DocsController::class, 'index'])->name('docs.index');
Route::resource('phones', \App\Http\Controllers\PhoneController::class)->only(['index', 'show']);
