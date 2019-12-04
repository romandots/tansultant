<?php
/**
 * File: auth.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['api'],
    'prefix' => 'manager_api/v1'
], static function () {
    Route::post('login', 'AuthController@login');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'manager_api/v1'
], static function () {
    Route::post('logout', 'AuthController@logout');

    Route::get('user', 'UserController@me');
    Route::patch('user/password', 'UserController@updatePassword');
});
