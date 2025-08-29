<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', [PublicController::class, 'home'])->name('home');

// Articles
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [PublicController::class, 'articles'])->name('index');
    Route::get('/{article:slug}', [PublicController::class, 'article'])->name('show');
    Route::post('/{article:slug}/comments', [PublicController::class, 'storeComment'])->name('comments.store');
});

// Comments
Route::post('/comments/{comment}/like', [PublicController::class, 'likeComment'])->name('comments.like');
Route::post('/comments/{comment}/reply', [PublicController::class, 'replyComment'])->name('comments.reply');

// Games
Route::prefix('games')->name('games.')->group(function () {
    Route::get('/', [PublicController::class, 'games'])->name('index');
    Route::get('/{game:slug}', [PublicController::class, 'game'])->name('show');
});

// Search
Route::get('/search', [PublicController::class, 'search'])->name('search');

// Category pages
Route::get('/category/{category:slug}', [PublicController::class, 'category'])->name('category.show');

// Tag pages
Route::get('/tag/{tag:slug}', [PublicController::class, 'tag'])->name('tag.show');

/*
|--------------------------------------------------------------------------
| User Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include admin routes
require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
