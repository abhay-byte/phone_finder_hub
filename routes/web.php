<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

// ─────────────────────────────────────────────────────────────────────────────
// Public routes – accessible to everyone (no auth required)
// ─────────────────────────────────────────────────────────────────────────────

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

// Logout handles its own auth check to avoid 'Please log in' errors on double-click
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ─────────────────────────────────────────────────────────────────────────────
// Guest-only routes (redirect authenticated users away)
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ─────────────────────────────────────────────────────────────────────────────
// Authenticated routes (auth required)
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    // User Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ─────────────────────────────────────────────────────────────────────────────
// Admin-only routes (auth + super_admin role required)
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/phones', [AdminController::class, 'index'])->name('phones.index');
    Route::get('/phones/add', [AdminController::class, 'addPhone'])->name('phones.add');
    Route::post('/phones/import', [AdminController::class, 'storePhone'])->name('phones.import');
    Route::get('/phones/{phone}/edit', [AdminController::class, 'editPhone'])->name('phones.edit');
    Route::put('/phones/{phone}', [AdminController::class, 'updatePhone'])->name('phones.update');
    Route::get('/phones/status/{jobId}', [AdminController::class, 'importStatusPage'])->name('phones.status');
    Route::get('/phones/status/{jobId}/json', [AdminController::class, 'importStatus'])->name('phones.status.json');
});
