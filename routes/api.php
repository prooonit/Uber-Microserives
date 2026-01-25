<?php

use App\Http\Controllers\DriverLocationController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\AuthDriverController;
use App\Http\Middleware\DecodeJwtMiddleware;

Route::prefix('user')->group(function () {
    Route::post('/register', [AuthUserController::class, 'register']);
    Route::post('/login', [AuthUserController::class, 'login']);

    
    Route::middleware(['user.jwt.decode'])->group(function () {
        Route::get('/nearbyDrivers', [DriverLocationController::class, 'nearbyDrivers']);
        Route::post('/estimate-fare', [DriverLocationController::class, 'estimateFare']);
        Route::post('/request-ride', [DriverLocationController::class, 'requestRide']);
    });
});






Route::prefix('driver')->group(function () {
    Route::post('/register', [AuthDriverController::class, 'register']);
    Route::post('/login', [AuthDriverController::class, 'login']);

    Route::middleware(['driver.jwt.decode'])->group(function () {
        Route::post('/status', [DriverLocationController::class, 'updateStatus']);
    });

});
