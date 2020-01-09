<?php
/**
 * File: auth.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api'], static function () {
    Route::post('register', 'RegistrationController@registerUser');
    Route::post('register/verify', 'RegistrationController@checkVerificationCode');
    Route::post('login', 'AuthController@login');
    Route::post('user/password/reset', 'ResetPasswordController@reset');
});

Route::group(['middleware' => 'member_api'], static function () {
    Route::post('logout', 'AuthController@logout');
    Route::get('user', 'UserController@me');
    Route::patch('user/password', 'UserController@updatePassword');
});
