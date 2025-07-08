<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\HRController;

use Modules\HR\Http\Controllers\DashboardController;
use Modules\HR\Http\Controllers\EmployeeManagement\KepegawaianController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('hrs', HRController::class)->names('hr');
});

Route::group(['middleware' => 'auth'], function () {
	Route::get('/dashboard', [DashboardController::class, 'index']);

	// Kepegawaian

	Route::prefix('kepegawaian')->group(function () {

		Route::get('/', [KepegawaianController::class, 'index'])->name('kepegawaian.index');
		// Route::prefix('pegawai')->group(function () {
		// 	Route::group(['middleware' => 'role:admin,kepsek,wakasek,pembinayys,ketuayys,direktur,etl,etm,fam,faspv,am,aspv'], function () {
		// 		Route::get('/', [PegawaiController::class, 'index'])->name('pegawai.index');
		// 		Route::get('ekspor', [PegawaiController::class, 'export'])->name('pegawai.ekspor')->middleware('role:etl,aspv');
		// 		Route::get('{id}', [PegawaiController::class, 'show'])->where('id', '[0-9]+')->name('pegawai.detail');
		// 	});
		// 	Route::group(['middleware' => 'role:etm'], function () {
		// 		Route::get('tambah', [PegawaiController::class, 'create'])->name('pegawai.tambah');
		// 		Route::post('simpan', [PegawaiController::class, 'store'])->name('pegawai.simpan');
		// 		Route::get('{id}/ubah', [PegawaiController::class, 'edit'])->where('id', '[0-9]+')->name('pegawai.ubah');
		// 		Route::put('{id}/perbarui', [PegawaiController::class, 'update'])->where('id', '[0-9]+')->name('pegawai.perbarui');
		// 	});
		// 	// Route::delete('{id}/hapus', [PegawaiController::class, 'destroy'])->where('id', '[0-9]+')->name('pegawai.hapus');
		// 	Route::put('{id}/validasi', [PegawaiController::class, 'accept'])->where('id', '[0-9]+')->name('pegawai.validasi')->middleware('role:faspv');
		// 	Route::put('{id}/reset', [PegawaiController::class, 'reset'])->where('id', '[0-9]+')->name('pegawai.reset')->middleware('role:admin');
		// 	Route::post('impor', [PegawaiController::class, 'import'])->name('pegawai.impor')->middleware('role:etm');
		// });
	});
});