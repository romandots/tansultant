<?php
/**
 * File: public_api.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'branches'], static function () {
    Route::get('/', 'BranchController@index');
    Route::get('{id}', 'BranchController@show');
});

Route::group(['prefix' => 'classrooms'], static function () {
    Route::get('/', 'ClassroomController@index');
    Route::get('{id}', 'ClassroomController@show');
});

Route::group(['prefix' => 'schedule'], static function () {
    Route::get('date', 'ScheduleController@onDate');
});
