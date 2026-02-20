<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentUpvoteController;
use App\Http\Controllers\AdminCommentController;

// ─────────────────────────────────────────────────────────────────────────────
// Public routes – accessible to everyone (no auth required)
// ─────────────────────────────────────────────────────────────────────────────

// Public Forum Routes
Route::get('/forum', [\App\Http\Controllers\ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/c/{category:slug}', [\App\Http\Controllers\ForumController::class, 'category'])->name('forum.category');
Route::get('/forum/p/{post:slug}', [\App\Http\Controllers\ForumController::class, 'show'])->name('forum.post.show');

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

    // Comments & Upvotes
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/upvote', [CommentUpvoteController::class, 'toggle'])->name('comments.upvote.toggle');

    // Forum endpoint for creating posts and replying
    Route::get('/forum/c/{category:slug}/create', [\App\Http\Controllers\ForumController::class, 'create'])->name('forum.post.create');
    Route::post('/forum/c/{category:slug}/create', [\App\Http\Controllers\ForumController::class, 'store'])->name('forum.post.store');
    Route::post('/forum/p/{post:slug}/reply', [\App\Http\Controllers\ForumController::class, 'reply'])->name('forum.post.reply');
});

// Comments (Public)
Route::get('/phones/{phone}/comments', [CommentController::class, 'index'])->name('phones.comments.index');
Route::post('/phones/{phone}/comments', [CommentController::class, 'store'])->name('comments.store');

// ─────────────────────────────────────────────────────────────────────────────
// Admin-only routes (auth + admin_panel access required)
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:maintainer,moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User & Role Management
    Route::middleware('super_admin')->group(function () {
        Route::get('/users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/role', [\App\Http\Controllers\AdminUserController::class, 'updateRole'])->name('users.role.update');
    });
    
    // Phones Management
    Route::middleware('role:maintainer')->group(function () {
        Route::get('/phones', [AdminController::class, 'index'])->name('phones.index');
        Route::get('/phones/add', [AdminController::class, 'addPhone'])->name('phones.add');
        Route::post('/phones/import', [AdminController::class, 'storePhone'])->name('phones.import');
        Route::get('/phones/{phone}/edit', [AdminController::class, 'editPhone'])->name('phones.edit');
        Route::put('/phones/{phone}', [AdminController::class, 'updatePhone'])->name('phones.update');
        Route::get('/phones/status/{jobId}', [AdminController::class, 'importStatusPage'])->name('phones.status');
        Route::get('/phones/status/{jobId}/json', [AdminController::class, 'importStatus'])->name('phones.status.json');
    });

    // Moderation (Comments & Forums)
    Route::middleware('role:moderator')->group(function () {
        // Comments Management
        Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/comments/{comment}/reply', [AdminCommentController::class, 'reply'])->name('comments.reply');

        // Forum Management
        Route::resource('forums/categories', \App\Http\Controllers\Admin\AdminForumCategoryController::class)->names('forum.categories');
        Route::resource('forums/posts', \App\Http\Controllers\Admin\AdminForumPostController::class)->only(['index', 'show', 'destroy'])->names('forum.posts');
        Route::delete('forums/comments/{comment}', [\App\Http\Controllers\Admin\AdminForumPostController::class, 'destroyComment'])->name('forum.comments.destroy');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Admin/Author routes (auth + author_admin role required)
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'author_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('blogs', \App\Http\Controllers\AdminBlogController::class);
    Route::post('blogs/upload-image', [\App\Http\Controllers\AdminBlogController::class, 'uploadImage'])->name('blogs.upload-image');
});

// ─────────────────────────────────────────────────────────────────────────────
// Public Blog Routes
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/blogs', [\App\Http\Controllers\BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{blog:slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blogs.show');
