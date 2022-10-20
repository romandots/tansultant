<?php
/**
 * File: payment.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

return [
    'name_presets' => [
        'visit' => 'Оплата посещения урока :lesson'
    ],
    'status' => [
        'pending' => 'Ожидает оплаты',
        'expired' => 'Просрочен',
        'confirmed' => 'Подтвержден',
        'canceled' => 'Отменён',
    ],
    'transfer_type' => [
        'cash' => 'Наличный',
        'card' => 'По карте',
        'online' => 'Онлайн',
        'internal' => 'Внутренний',
        'code' => 'Код'
    ]
];
