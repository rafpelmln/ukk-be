<?php

use App\Http\Controllers\Api\AuthParticipantsController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthParticipantsController::class, 'register']);
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
