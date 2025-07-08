<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

use Modules\Core\Http\Controllers\API\RegionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cores', CoreController::class)->names('core');
});

Route::post('fetch-cities', [RegionController::class, 'fetchCities']);
Route::post('fetch-subdistricts', [RegionController::class, 'fetchSubdistricts']);
Route::post('fetch-villages', [RegionController::class, 'fetchVillages']);