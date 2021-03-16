<?php
/**
 * File: student_api.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-7
 * Copyright (c) 2020
 */

/**
 * Неубличный АПИ для клиентских приложений
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Tags
Route::get('genres', 'GenreController@index');
Route::get('genres/my', 'GenreController@getSubscriptions');
Route::post('genres/{genre}', 'GenreController@subscribe');
Route::delete('genres/{genre}', 'GenreController@unsubscribe');
