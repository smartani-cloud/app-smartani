<?php

use Illuminate\Support\Facades\Route;
use Modules\FarmManagement\Http\Controllers\FarmManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('farmmanagements', FarmManagementController::class)->names('farmmanagement');
});
