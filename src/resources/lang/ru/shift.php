<?php

return [
    'name' => 'Смена администратора :user от :date',
    'status' => [
        \App\Models\Enum\ShiftStatus::ACTIVE->value => 'Активна',
        \App\Models\Enum\ShiftStatus::CLOSED->value => 'Закрыта',
    ],
];