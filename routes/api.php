<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RfidController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ESP32 RFID Reader API Endpoints
Route::prefix('esp32')->group(function () {
    Route::post('/scan', [RfidController::class, 'scan']);
    Route::post('/pay', [RfidController::class, 'pay']);
    Route::post('/heartbeat', [RfidController::class, 'heartbeat']);
});
