<?php

return [
    'status' => [
        \App\Models\Enum\TariffStatus::ACTIVE->value => 'Активный',
        \App\Models\Enum\TariffStatus::ARCHIVED->value => 'Архивный',
    ],
];
