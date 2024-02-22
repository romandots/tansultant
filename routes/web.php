<?php

use Illuminate\Support\Facades\Route;

Route::get('swagger.yaml', 'Swagger@yaml')->name('swagger.yaml');
Route::get('swagger.json', 'Swagger@json')->name('swagger.source');
//Route::get('swagger', 'Swagger@ui');

Route::get('dashboard', 'SystemMonitor@dashboard')->name('monitor.dashboard');
Route::get('redischeck', 'SystemMonitor@redisHealthCheck')->name('monitor.redis');
Route::get('dbcheck', 'SystemMonitor@databaseHealthCheck')->name('monitor.db');
Route::get('servicescheck', 'SystemMonitor@servicesHealthCheck')->name('monitor.services');