<?php
/**
 * File: api.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

use App\Services\Permissions\CoursesPermissions;
use App\Services\Permissions\CustomersPermissions;
use App\Services\Permissions\InstructorsPermissions;
use App\Services\Permissions\PersonsPermissions;
use App\Services\Permissions\SchedulesPermissions;
use App\Services\Permissions\StudentsPermissions;
use App\Services\Permissions\UsersPermissions;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'auth:api'
], static function () {
    Route::get('user', 'UserController@me');
    Route::patch('user/password', 'UserController@updatePassword');

    Route::post('users/from_person', 'UserController@createFromPerson')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::CREATE_USERS);
    Route::post('users', 'UserController@store')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::CREATE_USERS);
    Route::get('users/{id}', 'UserController@show')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::READ_USERS);
    Route::patch('users/{id}', 'UserController@update')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::UPDATE_USERS);
    Route::delete('users/{id}', 'UserController@destroy')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::DELETE_USERS);

    Route::post('people', 'PersonController@store')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::CREATE_PERSONS);
    Route::get('people/{id}', 'PersonController@show')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::READ_PERSONS);
    Route::patch('people/{id}', 'PersonController@update')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::UPDATE_PERSONS);
    Route::delete('people/{id}', 'PersonController@destroy')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::DELETE_PERSONS);

    Route::post('students/from_person', 'StudentController@createFromPerson')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::CREATE_STUDENTS);
    Route::post('students', 'StudentController@store')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::CREATE_STUDENTS);
    Route::get('students/{id}', 'StudentController@show')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::READ_STUDENTS);
    Route::patch('students/{id}', 'StudentController@update')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::UPDATE_STUDENTS);
    Route::delete('students/{id}', 'StudentController@destroy')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::DELETE_STUDENTS);

    Route::post('instructors/from_person', 'InstructorController@createFromPerson')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::CREATE_INSTRUCTORS);
    Route::post('instructors', 'InstructorController@store')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::CREATE_INSTRUCTORS);
    Route::get('instructors/{id}', 'InstructorController@show')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::READ_INSTRUCTORS);
    Route::patch('instructors/{id}', 'InstructorController@update')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::UPDATE_INSTRUCTORS);
    Route::delete('instructors/{id}', 'InstructorController@destroy')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::DELETE_INSTRUCTORS);

    Route::post('customers/from_person', 'CustomerController@createFromPerson')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::CREATE_CUSTOMERS);
    Route::post('customers', 'CustomerController@store')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::CREATE_CUSTOMERS);
    Route::get('customers/{id}', 'CustomerController@show')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::READ_CUSTOMERS);
    Route::delete('customers/{id}', 'CustomerController@destroy')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::DELETE_CUSTOMERS);
    Route::get('contracts/{contract}', 'ContractController@show')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::READ_CUSTOMERS);
    Route::post('contracts/{contract}', 'ContractController@sign')
        ->middleware('permission:' . CustomersPermissions::SIGN_CONTRACTS);
    Route::delete('contracts/{contract}', 'ContractController@terminate')
        ->middleware('permission:' . CustomersPermissions::TERMINATE_CONTRACTS);

    Route::post('courses', 'CourseController@store')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::CREATE_COURSES);
    Route::patch('courses/{id}', 'CourseController@update')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::UPDATE_COURSES);
    Route::delete('courses/{id}', 'CourseController@destroy')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::DELETE_COURSES);
    Route::get('courses/{id}', 'CourseController@show')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::READ_COURSES);

    Route::get('schedules/date', 'ScheduleController@onDate')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::READ_SCHEDULES);
    Route::post('schedules', 'ScheduleController@store')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::CREATE_SCHEDULES);
    Route::patch('schedules/{id}', 'ScheduleController@update')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::UPDATE_SCHEDULES);
    Route::delete('schedules/{id}', 'ScheduleController@destroy')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::DELETE_SCHEDULES);
    Route::get('schedules/{id}', 'ScheduleController@show')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::READ_SCHEDULES);
});
