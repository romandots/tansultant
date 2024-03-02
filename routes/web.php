<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::group(['middleware' => ['web']], function () {
    Route::get('swagger.yaml', 'Swagger@yaml')->name('swagger.yaml');
    Route::get('swagger.json', 'Swagger@json')->name('swagger.source');
    //Route::get('swagger', 'Swagger@ui');


    Route::get('dashboard', 'SystemMonitor@dashboard')->name('monitor.dashboard');
    Route::get('health', [HealthCheckResultsController::class, '__invoke'])->name('monitor.healthcheck');
});