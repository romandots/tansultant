<?php

return [
    'name' => 'Смена :user, :date',
    'status' => [
        \App\Models\Enum\ShiftStatus::ACTIVE->value => 'Активна',
        \App\Models\Enum\ShiftStatus::CLOSED->value => 'Закрыта',
    ],
];