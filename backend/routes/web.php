<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Observability & Health Check Probe
Route::get('/health', [HealthController::class, 'check']);
