<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// route group

/*
|--------------------------------------------------------------------------
| Protected Routes -> ROute group = harus ke autentikasi (butuh token)
|--------------------------------------------------------------------------
|
| - route group for authenticated users
|    a. route group for ADMIN
|    b. route group for ATTENDEE
|
*/
Route::middleware('auth:sanctum')->group(function () {
    // Get User
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Get Event Index
    Route::get('/event', [EventController::class, 'index']);
    // Get Event Detail
    Route::get('/event/{eventId}', [EventController::class, 'show']);

    // ADMIN ONLY
    Route::group(['middleware' => ['role:admin']], function () {
        // Create Event
        Route::post('/event', [EventController::class, 'store']);
        // Update Event
        Route::post('/event/{eventId}', [EventController::class, 'update']);
        // Delete Event
        Route::delete('/event/{eventId}', [EventController::class, 'delete']);
    });

    // ATTENDEE ONLY
    Route::group(['middleware' => ['role:attendee']], function () {
        // 
    });
});

// Guest Route
Route::group([], function () {
    // Register
    Route::post('/register', [AuthController::class, 'register']);
    // Login
    Route::post('/login', [AuthController::class, 'login']);
});
