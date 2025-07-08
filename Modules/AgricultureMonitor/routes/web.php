<?php

use Illuminate\Support\Facades\Route;
use Modules\AgricultureMonitor\Http\Controllers\AgricultureMonitorController;

use Modules\AgricultureMonitor\Http\Controllers\SensorController;

Route::middleware(['auth'])->group(function () {
	Route::prefix('reference')->group(function () {
		Route::prefix('sensor')->name('sensor')->group(function () {
			Route::get('/', [SensorController::class, 'index'])->name('.index');
			Route::post('store', [SensorController::class, 'store'])->name('.store');
			Route::post('edit', [SensorController::class, 'edit'])->name('.edit');
			Route::put('update', [SensorController::class, 'update'])->name('.update');
			Route::delete('{id}/destroy', [SensorController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
		});
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('agriculturemonitors', AgricultureMonitorController::class)->names('agriculturemonitor');
});
