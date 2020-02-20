<?php
/**
 * File: course.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

return [
    'status' => [
        'active' => 'Работает',
        'pending' => 'Идёт набор',
        'disabled' => 'Закрыт',
    ],
    'age_restrictions' => [
        'from_to' => 'от :from до :to',
        'from' => ':from+',
        'to' => 'до :to',
        'any' => 'без возрастных ограничений',
    ]
];
