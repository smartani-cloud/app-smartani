<?php

use Illuminate\Support\Facades\Route;
use Modules\AgricultureMonitor\Http\Controllers\AgricultureMonitorController;

use Modules\AgricultureMonitor\Http\Controllers\SensorController;
use Modules\AgricultureMonitor\Http\Controllers\SensorFeedbackController;

Route::middleware(['auth'])->group(function () {
	Route::prefix('sensor-feedback')->name('sensor-feedback')->group(function () {
		Route::get('/', [SensorFeedbackController::class, 'index'])->name('.index');
		Route::post('store', [SensorFeedbackController::class, 'store'])->name('.store');
		Route::post('edit', [SensorFeedbackController::class, 'edit'])->name('.edit');
		Route::put('update', [SensorFeedbackController::class, 'update'])->name('.update');
		Route::delete('{id}/destroy', [SensorFeedbackController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
	});
	Route::prefix('reference')->group(function () {
		Route::prefix('sensor-list')->name('sensor-list')->group(function () {
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
