<?php

use App\Http\Controllers\Api\AuthController;
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
});

// Guest Route
Route::group([], function () {
    // Register
    Route::post('/register', [AuthController::class, 'register']);
    // Login
    Route::post('/login', [AuthController::class, 'login']);
});
