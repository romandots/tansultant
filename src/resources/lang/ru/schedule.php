<?php
/**
 * File: course.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

return [
    'cycle' => [
        \App\Models\Enum\ScheduleCycle::ONCE->value => ':date :time',
        \App\Models\Enum\ScheduleCycle::EVERY_DAY->value => 'Каждый день в :time',
        \App\Models\Enum\ScheduleCycle::EVERY_WEEK->value => ':weekday в :time',
        \App\Models\Enum\ScheduleCycle::EVERY_MONTH->value => ':day числа в :time',
    ],
    'weekday' => [
        \App\Models\Enum\Weekday::MONDAY->value => 'Понедельник',
        \App\Models\Enum\Weekday::TUESDAY->value => 'Вторник',
        \App\Models\Enum\Weekday::WEDNESDAY->value => 'Среда',
        \App\Models\Enum\Weekday::THURSDAY->value => 'Четверг',
        \App\Models\Enum\Weekday::FRIDAY->value => 'Пятница',
        \App\Models\Enum\Weekday::SATURDAY->value => 'Суббота',
        \App\Models\Enum\Weekday::SUNDAY->value => 'Воскресенье',
    ],
];
