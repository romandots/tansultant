<?php

use Illuminate\Support\Facades\Route;

Route::get('swagger.yaml', 'Swagger@yaml')->name('swagger.yaml');
Route::get('swagger.json', 'Swagger@json')->name('swagger.source');
//Route::get('swagger', 'Swagger@ui');