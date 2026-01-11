<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HealthController;

Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/ping', [HealthController::class, 'ping']);

    // Protected routes would go here
    Route::middleware('auth:sanctum')->group(function () {
        // Add authenticated routes here
    });
});
