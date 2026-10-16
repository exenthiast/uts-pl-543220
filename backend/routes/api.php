<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Login is the only public endpoint. All other endpoints strictly require
| a valid Laravel Sanctum Bearer token.
|
*/

// Public Authentication Endpoint
Route::post('/login', [AuthController::class, 'login']);

// Protected Endpoints (Requires Sanctum Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // CRUD Book Resource
    Route::apiResource('books', BookController::class);
});
