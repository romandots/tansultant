<?php

return [
    'enable_mock' => (bool)env('TOCHKA_ENABLE_MOCK', false),
    'api' => [
        'host' => env('TOCHKA_SBP_API_HOST', 'https://enter.tochka.com/sandbox/v2/sbp/v1.0'),
        'client_id' => env('TOCHKA_CLIENT_ID'),
        'client_secret' => env('TOCHKA_CLIENT_SECRET'),
        'redirect_uri' => env('TOCHKA_AUTH_REDIRECT_URI'),
    ],
    'account' => [
        'account_id' => env('TOCHKA_ACCOUNT_ID'), // Уникальный и неизменный идентификатор счёта юрлица
        'scopes' => 'sbp',
        'permissions' => [
            'ReadAccountsBasic',
            'ReadAccountsDetail',
            'MakeAcquiringOperation',
            'ReadAcquiringData',
            'ReadBalances',
            'ReadTransactionsDetail',
            'ReadTransactionsCredits',
            'ReadTransactionsDebits',
            'ReadStatements',
            'ReadCustomerData',
            'ReadSBPData',
            'EditSBPData',
            'CreatePaymentForSign',
            'CreatePaymentOrder',
            'ReadSpecialAccounts',
        ],
    ],
    'qr' => [
        'width' => 200,
        'height' => 200,
        'media_type' => 'image/png',
        'ttl' => 60,
        'redirect_url' => env('TOCHKA_PAYMENT_REDIRECT_URL'), // URL, на который будет перенаправлен пользователь после завершения оплаты
    ],
    'webhook' => [
        'secret' => env('TOCHKA_WEBHOOK_SECRET'),
    ],
];