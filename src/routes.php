<?php

use Illuminate\Support\Facades\Route;
use LangtonMwanza\PulseDevops\Http\Controllers\MigrationsController;
use LangtonMwanza\PulseDevops\Http\Controllers\GcpSecretsController;
use LangtonMwanza\PulseDevops\Http\Controllers\CloudRunJobsController;
use LangtonMwanza\PulseDevops\Http\Controllers\DatabaseController;
use LangtonMwanza\PulseDevops\Http\Middleware\AuthorizeDevops;

Route::middleware(['web', AuthorizeDevops::class])
    ->prefix('pulse/devops')
    ->group(function () {
        Route::get('migrations', [MigrationsController::class, 'index'])->name('pulse-devops.migrations');
        Route::post('migrations/run', [MigrationsController::class, 'run'])->name('pulse-devops.migrations.run');
        Route::post('migrations/rollback', [MigrationsController::class, 'rollback'])->name('pulse-devops.migrations.rollback');

        Route::get('database', [DatabaseController::class, 'index'])->name('pulse-devops.database');

        Route::get('secrets', [GcpSecretsController::class, 'index'])->name('pulse-devops.secrets');
        Route::post('secrets', [GcpSecretsController::class, 'store'])->name('pulse-devops.secrets.store');
        Route::put('secrets/{name}', [GcpSecretsController::class, 'update'])->name('pulse-devops.secrets.update');
        Route::get('secrets/{name}/value', [GcpSecretsController::class, 'value'])->name('pulse-devops.secrets.value');

        Route::get('cloud-run-jobs', [CloudRunJobsController::class, 'index'])->name('pulse-devops.cloud-run-jobs');
        Route::post('cloud-run-jobs/{name}/execute', [CloudRunJobsController::class, 'execute'])->name('pulse-devops.cloud-run-jobs.execute');
    });
