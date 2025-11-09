<?php

use App\Http\Controllers\Api\AuthParticipantsController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\HomeBannerController as ApiHomeBannerController;
use App\Http\Controllers\Api\LeadershipStructureController as ApiLeadershipStructureController;
use App\Http\Controllers\Api\VisionMissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthParticipantsController::class, 'register'])->name('api.participants.register');
    Route::post('/login', [AuthParticipantsController::class, 'login']);
    Route::post('/logout', [AuthParticipantsController::class, 'logout']);
    Route::get('/me', [AuthParticipantsController::class, 'me']);
});

// Participant position requests
use App\Http\Controllers\Api\PositionRequestController;

Route::get('/position-requests', [PositionRequestController::class, 'index']);
Route::post('/position-requests', [PositionRequestController::class, 'store']);
Route::delete('/position-requests/{id}', [PositionRequestController::class, 'destroy']);

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working', 'timestamp' => now()]);
});

// News
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);

// Events
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// Home banners
Route::get('/home-banners', [ApiHomeBannerController::class, 'index']);
Route::get('/leadership-structures', [ApiLeadershipStructureController::class, 'index'])->name('api.leadership-structures.index');
Route::get('/vision-mission', [VisionMissionController::class, 'index'])->name('api.vision-mission.index');

// Bank accounts
use App\Http\Controllers\Api\BankAccountController;
Route::get('/bank-accounts', [BankAccountController::class, 'index']);

// Event orders
use App\Http\Controllers\Api\EventOrderController;
Route::get('/event-orders', [EventOrderController::class, 'index']);
Route::post('/event-orders', [EventOrderController::class, 'store']);
Route::get('/event-orders/{id}', [EventOrderController::class, 'show']);
Route::post('/event-orders/{id}/upload-payment-proof', [EventOrderController::class, 'uploadPaymentProof']);

use App\Http\Controllers\Api\ActivityController as ApiActivityController;
Route::get('/activities', [ApiActivityController::class, 'index']);
Route::get('/activities/{activity}', [ApiActivityController::class, 'show']);
Route::post('/activities/{activity}/check-in', [ApiActivityController::class, 'checkIn']);
