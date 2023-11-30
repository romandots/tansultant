<?php

return [
    'api' => [
        'host' => 'https://enter.tochka.com/sandbox/v2/sbp/v1.0',
        'client_id' => env('TOCHKA_CLIENT_ID'),
        'client_secret' => env('TOCHKA_CLIENT_SECRET'),
        'redirect_uri' => env('TOCHKA_AUTH_REDIRECT_URI'),
    ],
    'account' => [
        'id' => env('TOCHKA_ACCOUNT_ID'),
        'currency' => env('TOCHKA_ACCOUNT_CURRENCY'),
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
        'legal_id' => env('TOCHKA_LEGAL_ID'), // Уникальный и неизменный идентификатор счёта юрлица
        'merchant_id' => env('TOCHKA_MERCHANT_ID'), // Идентификатор ТСП в СБП
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