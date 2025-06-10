<?php
/**
 * File: lesson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

return [
    'lesson' => 'Урок',
    'lessons' => 'Уроки',

    'type' => [
        \App\Models\Enum\LessonType::LESSON->value => 'Урок',
        \App\Models\Enum\LessonType::EVENT->value => 'Событие',
        \App\Models\Enum\LessonType::RENT->value => 'Аренда',
    ],

    'status' => [
        \App\Models\Enum\LessonStatus::BOOKED->value => 'Забронирован',
        \App\Models\Enum\LessonStatus::ONGOING->value => 'Идёт',
        \App\Models\Enum\LessonStatus::PASSED->value => 'Закончен',
        \App\Models\Enum\LessonStatus::CANCELED->value => 'Отменён',
        \App\Models\Enum\LessonStatus::CLOSED->value => 'Закрыт',
        \App\Models\Enum\LessonStatus::CHECKED_OUT->value => 'Рассчитан',
    ],
];
