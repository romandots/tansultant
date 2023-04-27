<?php
/**
 * File: bonus.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

return [
    'status' => [
        'pending' => 'Ожидает подтверждения',
        'approved' => 'Активный',
        'disabled' => 'Отключен',
    ],
    'role' => [
        'student' => 'Студент',
        'instructor' => 'Преподаватель',
        'assistant' => 'Ассистент',
        'manager' => 'Менеджер',
        'admin' => 'Супер-администратор',
    ],
];
