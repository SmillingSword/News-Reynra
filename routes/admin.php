<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsScraperController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group.
|
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Articles Management
    Route::resource('articles', ArticleController::class);
    Route::post('articles/bulk-action', [ArticleController::class, 'bulkAction'])->name('articles.bulk-action');

    // Categories Management
    Route::resource('categories', CategoryController::class)->parameters(['categories' => 'category:slug']);
    Route::patch('categories/{category:slug}/toggle-active', [CategoryController::class, 'toggleActive'])->name('categories.toggle-active');
    Route::post('categories/update-order', [CategoryController::class, 'updateOrder'])->name('categories.update-order');
    Route::get('categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');

    // Tags Management
    Route::resource('tags', TagController::class)->parameters(['tags' => 'tag:slug']);
    Route::patch('tags/{tag:slug}/toggle-featured', [TagController::class, 'toggleFeatured'])->name('tags.toggle-featured');
    Route::get('tags/popular', [TagController::class, 'popular'])->name('tags.popular');

    // Games Management
    Route::resource('games', GameController::class)->parameters(['games' => 'game:slug']);
    Route::patch('games/{game:slug}/toggle-featured', [GameController::class, 'toggleFeatured'])->name('games.toggle-featured');
    Route::patch('games/{game:slug}/toggle-active', [GameController::class, 'toggleActive'])->name('games.toggle-active');
    Route::get('games/featured', [GameController::class, 'featured'])->name('games.featured');
    
    // Users Management
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-verification', [UserController::class, 'toggleVerification'])->name('users.toggle-verification');
    Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    
    // Comments Management
    Route::resource('comments', CommentController::class)->only(['index', 'show', 'update', 'destroy']);
    
    // Media Library
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    
    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    
    // Roles & Permissions
    Route::resource('roles', RoleController::class);
    
    // News Scraper
    Route::prefix('news-scraper')->name('news-scraper.')->group(function () {
        Route::get('/', [NewsScraperController::class, 'index'])->name('index');
        Route::get('/stats', [NewsScraperController::class, 'stats'])->name('stats');
        Route::post('/scrape-all', [NewsScraperController::class, 'scrapeAll'])->name('scrape-all');
        
        Route::prefix('sources')->name('sources.')->group(function () {
            Route::get('/{newsSource}', [NewsScraperController::class, 'show'])->name('show');
            Route::post('/{newsSource}/scrape', [NewsScraperController::class, 'scrape'])->name('scrape');
            Route::put('/{newsSource}/config', [NewsScraperController::class, 'updateConfig'])->name('update-config');
            Route::post('/{newsSource}/test', [NewsScraperController::class, 'testScraping'])->name('test');
        });
        
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/{scrapingJob}', [NewsScraperController::class, 'jobDetails'])->name('details');
            Route::post('/{scrapingJob}/retry', [NewsScraperController::class, 'retryJob'])->name('retry');
        });
    });
});
