<?php
/**
 * File: log_record.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

return [
    \App\Models\Course::class => [
        \App\Models\Enum\LogRecordAction::CREATE->value => ':user создаёт класс :object',
    ]
];
