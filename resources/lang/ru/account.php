<?php
/**
 * File: account.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

return [
    'name_presets' => [
        'student' => 'Л/С :student',
        'instructor' => 'Л/С :instructor',
        'branch_savings' => 'Депозитный счёт :branch',
        'branch_operational' => 'Операционный счет :branch'
    ],
    'type' => [
        'operational' => 'Операционный',
        'savings' => 'Дебетовый',
        'personal' => 'Личный',
    ]
];
