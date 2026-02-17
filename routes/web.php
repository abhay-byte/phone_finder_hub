<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhoneController; // Added this use statement for brevity in routes

Route::get('/', [PhoneController::class, 'index'])->name('home');
Route::get('/phones/search', [PhoneController::class, 'search'])->name('phones.search');
Route::get('/phones/grid', [PhoneController::class, 'grid'])->name('phones.grid');
Route::get('/rankings', [PhoneController::class, 'rankings'])->name('phones.rankings');
Route::get('/methodology/ueps', [PhoneController::class, 'uepsMethodology'])->name('methodology.ueps');
Route::get('/methodology/cms', [PhoneController::class, 'cmsMethodology'])->name('methodology.cms');
Route::get('/methodology/fpi', [PhoneController::class, 'fpiMethodology'])->name('methodology.fpi');
Route::get('/methodology/endurance', [PhoneController::class, 'enduranceMethodology'])->name('methodology.endurance');
Route::get('/methodology/gpx', [PhoneController::class, 'gpxMethodology'])->name('methodology.gpx');
Route::get('/compare', [\App\Http\Controllers\ComparisonController::class, 'index'])->name('phones.compare');
Route::get('/docs', [\App\Http\Controllers\DocsController::class, 'index'])->name('docs.index');
Route::get('/phones/{phone}', [PhoneController::class, 'show'])->name('phones.show');
