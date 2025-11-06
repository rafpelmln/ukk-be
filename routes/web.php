<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\GenerationController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\PositionRequestController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\HomeBannerController as AdminHomeBannerController;


Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // News Categories CRUD - must be before news resource
    Route::prefix('news/categories')->name('news.categories.')->group(function () {
        Route::get('/', [NewsCategoryController::class, 'index'])->name('index');
        Route::get('/create', [NewsCategoryController::class, 'create'])->name('create');
        Route::post('/', [NewsCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [NewsCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [NewsCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [NewsCategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{category}/toggle', [NewsCategoryController::class, 'toggle'])->name('toggle');
    });

    // News tags must be registered before news resource to avoid conflict with
    // the `news/{news}` parameter route that would capture `/news/tags`.
    Route::prefix('news/tags')->name('news.tags.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::get('/create', [TagController::class, 'create'])->name('create');
        Route::post('/', [TagController::class, 'store'])->name('store');
        Route::get('/{tag}/edit', [TagController::class, 'edit'])->name('edit');
        Route::put('/{tag}', [TagController::class, 'update'])->name('update');
        Route::delete('/{tag}', [TagController::class, 'destroy'])->name('destroy');
        Route::patch('/{tag}/toggle', [TagController::class, 'toggle'])->name('toggle');
    });

    // News CRUD
    Route::resource('news', NewsController::class);

    // Events management
    Route::resource('events', EventController::class)->except(['show']);
    Route::resource('home-banners', AdminHomeBannerController::class)->except(['show']);
    Route::patch('home-banners/{homeBanner}/toggle', [AdminHomeBannerController::class, 'toggle'])->name('home-banners.toggle');

    Route::resource('generations', GenerationController::class)->except(['show']);
    Route::resource('participants', ParticipantController::class)->except(['show']);
    Route::resource('positions', PositionController::class)->except(['show']);
    Route::patch('positions/{position}/toggle', [PositionController::class, 'toggle'])->name('positions.toggle');

    // Position Requests - for managing participant position applications
    Route::prefix('position-requests')->name('position-requests.')->group(function () {
        Route::get('/', [PositionRequestController::class, 'index'])->name('index');
        Route::get('/{positionRequest}', [PositionRequestController::class, 'show'])->name('show');
        Route::post('/{positionRequest}/approve', [PositionRequestController::class, 'approve'])->name('approve');
        Route::post('/{positionRequest}/reject', [PositionRequestController::class, 'reject'])->name('reject');
        Route::delete('/{positionRequest}', [PositionRequestController::class, 'destroy'])->name('destroy');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
