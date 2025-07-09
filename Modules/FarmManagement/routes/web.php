<?php

use Illuminate\Support\Facades\Route;
use Modules\FarmManagement\Http\Controllers\FarmManagementController;

// Dashboard
use Modules\FarmManagement\Http\Controllers\Home\DashboardController;

use Modules\FarmManagement\Http\Controllers\GreenhouseController;
use Modules\FarmManagement\Http\Controllers\GreenhouseOwnerController;
use Modules\FarmManagement\Http\Controllers\PlantController;
use Modules\FarmManagement\Http\Controllers\PlantingCycleController;
use Modules\FarmManagement\Http\Controllers\Reference\HarvestCategoryController;
use Modules\FarmManagement\Http\Controllers\Reference\HarvestQualityController;
use Modules\FarmManagement\Http\Controllers\Reference\IrrigationSystemController;
use Modules\FarmManagement\Http\Controllers\Reference\PlantCategoryController;
use Modules\FarmManagement\Http\Controllers\Reference\PlantTypeController;

Route::middleware(['auth'])->group(function () {
	Route::prefix('home')->name('dashboard')->group(function () {
		Route::get('/', [DashboardController::class, 'index'])->name('.index');
	});

	Route::prefix('greenhouse-list')->name('greenhouse-list')->group(function () {
		Route::get('/', [GreenhouseController::class, 'index'])->name('.index');
		Route::get('create', [GreenhouseController::class, 'create'])->name('.create');
		Route::post('store', [GreenhouseController::class, 'store'])->name('.store');
		Route::get('{id}', [GreenhouseController::class, 'show'])->name('.show');
		Route::get('{id}/edit', [GreenhouseController::class, 'edit'])->name('.edit');
		Route::put('update', [GreenhouseController::class, 'update'])->name('.update');
		Route::delete('{id}/destroy', [GreenhouseController::class, 'destroy'])->name('.destroy');
	});

	Route::prefix('planting-cycle')->name('planting-cycle')->group(function () {
		Route::get('/', [PlantingCycleController::class, 'index'])->name('.index');
		Route::get('create', [PlantingCycleController::class, 'create'])->name('.create');
		Route::post('store', [PlantingCycleController::class, 'store'])->name('.store');
		Route::get('{id}', [PlantingCycleController::class, 'show'])->name('.show');
		Route::get('{id}/edit', [PlantingCycleController::class, 'edit'])->name('.edit');
		Route::put('update', [PlantingCycleController::class, 'update'])->name('.update');
		Route::delete('{id}/destroy', [PlantingCycleController::class, 'destroy'])->name('.destroy');
	});

	Route::prefix('greenhouse-owner')->name('greenhouse-owner')->group(function () {
		Route::get('/', [GreenhouseOwnerController::class, 'index'])->name('.index');
		Route::get('create', [GreenhouseOwnerController::class, 'create'])->name('.create');
		Route::post('store', [GreenhouseOwnerController::class, 'store'])->name('.store');
		Route::get('{id}', [GreenhouseOwnerController::class, 'show'])->where('id', '[0-9]+')->name('.show');
		Route::get('{id}/edit', [GreenhouseOwnerController::class, 'edit'])->where('id', '[0-9]+')->name('.edit');
		Route::put('update', [GreenhouseOwnerController::class, 'update'])->name('.update');
		Route::delete('{id}/destroy', [GreenhouseOwnerController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
	});

	Route::prefix('plant-list')->name('plant-list')->group(function () {
		Route::get('/', [PlantController::class, 'index'])->name('.index');
		Route::post('store', [PlantController::class, 'store'])->name('.store');
		Route::post('edit', [PlantController::class, 'edit'])->name('.edit');
		Route::put('update', [PlantController::class, 'update'])->name('.update');
		Route::delete('{id}/destroy', [PlantController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
	});

    Route::prefix('reference')->group(function () {
		Route::get('/', function () {
			return redirect()->route('harvest-category.index');
		})->name('reference.index');
		Route::prefix('harvest-category')->name('harvest-category')->group(function () {
			Route::get('/', [HarvestCategoryController::class, 'index'])->name('.index');
			Route::post('store', [HarvestCategoryController::class, 'store'])->name('.store');
			Route::post('edit', [HarvestCategoryController::class, 'edit'])->name('.edit');
			Route::put('update', [HarvestCategoryController::class, 'update'])->name('.update');
			Route::delete('{id}/destroy', [HarvestCategoryController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
		});
		Route::prefix('harvest-quality')->name('harvest-quality')->group(function () {
			Route::get('/', [HarvestQualityController::class, 'index'])->name('.index');
			Route::post('store', [HarvestQualityController::class, 'store'])->name('.store');
			Route::post('edit', [HarvestQualityController::class, 'edit'])->name('.edit');
			Route::put('update', [HarvestQualityController::class, 'update'])->name('.update');
			Route::delete('{id}/destroy', [HarvestQualityController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
		});
		Route::prefix('irrigation-system')->name('irrigation-system')->group(function () {
			Route::get('/', [IrrigationSystemController::class, 'index'])->name('.index');
			Route::post('store', [IrrigationSystemController::class, 'store'])->name('.store');
			Route::post('edit', [IrrigationSystemController::class, 'edit'])->name('.edit');
			Route::put('update', [IrrigationSystemController::class, 'update'])->name('.update');
			Route::delete('{id}/destroy', [IrrigationSystemController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
		});
		Route::prefix('plant-category')->name('plant-category')->group(function () {
			Route::get('/', [PlantCategoryController::class, 'index'])->name('.index');
			Route::post('store', [PlantCategoryController::class, 'store'])->name('.store');
			Route::post('edit', [PlantCategoryController::class, 'edit'])->name('.edit');
			Route::put('update', [PlantCategoryController::class, 'update'])->name('.update');
			Route::delete('{id}/destroy', [PlantCategoryController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
		});
		Route::prefix('plant-type')->name('plant-type')->group(function () {
			Route::get('/', [PlantTypeController::class, 'index'])->name('.index');
			Route::post('store', [PlantTypeController::class, 'store'])->name('.store');
			Route::post('edit', [PlantTypeController::class, 'edit'])->name('.edit');
			Route::put('update', [PlantTypeController::class, 'update'])->name('.update');
			Route::delete('{id}/destroy', [PlantTypeController::class, 'destroy'])->where('id', '[0-9]+')->name('.destroy');
		});
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('farmmanagements', FarmManagementController::class)->names('farmmanagement');
});

