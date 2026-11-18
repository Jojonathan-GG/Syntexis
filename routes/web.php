<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('connections', ConnectionController::class);
    Route::post('connections/{connection}/test', [ConnectionController::class, 'test'])
        ->name('connections.test');

    Route::resource('datasets', DatasetController::class)->only(['index', 'show']);

    Route::resource('pipelines', PipelineController::class);
    Route::post('pipelines/{pipeline}/run', [PipelineController::class, 'run'])
        ->name('pipelines.run');
    Route::post('pipelines/{pipeline}/toggle', [PipelineController::class, 'toggle'])
        ->name('pipelines.toggle');

    Route::get('insights', [InsightController::class, 'index'])->name('insights.index');
    Route::post('alerts/{alert}/acknowledge', [InsightController::class, 'acknowledge'])
        ->name('alerts.acknowledge');

    Route::get('admin', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
