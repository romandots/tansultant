<?php
/**
 * File: log_record.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

return [
    \App\Models\Enum\LogRecordObjectType::USER->value => [
        \App\Models\Enum\LogRecordAction::CREATE->value => ':user создаёт пользователя :object',
    ],
    \App\Models\Enum\LogRecordObjectType::COURSE->value => [
        \App\Models\Enum\LogRecordAction::CREATE->value => ':user создаёт курс :object',
    ],
];
