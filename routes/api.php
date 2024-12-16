<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;

// Define the version prefix for the API routes
Route::prefix('v1')->group(function () {

    // Route for registering a new user
    Route::post('/register', [AuthController::class, 'register']);
    // Route for logging in an existing user
    Route::post('/login', [AuthController::class, 'login']);

    // Group of routes that require user authentication
    Route::group(['middleware' => ['auth:sanctum']], function() {
        
        // Route for logging out the authenticated user from the current device
        Route::post('/logout', [AuthController::class, 'logout']);
        // Route for logging out the authenticated user from all devices
        Route::post('/logout-all', [AuthController::class, 'logoutFromAllDevices']);
        //Category routs
        Route::get('/category', [CategoryController::class, 'index']);
        Route::get('/category/{id}', [CategoryController::class, 'show']);
        Route::post('/category', [CategoryController::class, 'store']);
        Route::put('/category/{id}', [CategoryController::class, 'update']);
        Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
    });
});
