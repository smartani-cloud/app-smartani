<?php

use Illuminate\Support\Facades\Route;
use Modules\AgricultureMonitor\Http\Controllers\AgricultureMonitorController;

use Modules\AgricultureMonitor\Http\Controllers\SensorReadingController;

Route::post('sensor/data', [SensorReadingController::class, 'store']);

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('agriculturemonitors', AgricultureMonitorController::class)->names('agriculturemonitor');
});
