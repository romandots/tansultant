<?php

return [
    'status' => [
        \App\Models\Enum\SubscriptionStatus::ACTIVE->value => 'Активная',
        \App\Models\Enum\SubscriptionStatus::PENDING->value => 'Не активирована',
        \App\Models\Enum\SubscriptionStatus::EXPIRED->value => 'Просрочена',
        \App\Models\Enum\SubscriptionStatus::ON_HOLD->value => 'Приостановлена',
        \App\Models\Enum\SubscriptionStatus::NOT_PAID->value => 'Не оплачена',
        \App\Models\Enum\SubscriptionStatus::CANCELED->value => 'Отменена',
    ],
];
