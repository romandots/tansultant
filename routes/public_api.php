<?php
/**
 * File: public_api.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

/**
 * Публичный АПИ с открытой информацией
 * для сайтов, виджетов и приложений
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('ping', function () {
    return 'OK';
});
