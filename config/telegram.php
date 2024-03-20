<?php

return [
    'api_host' => env('TELEGRAM_API_HOST'),
    'api_key' => env('TELEGRAM_API_KEY'),
    'endpoints' => [
        'ping' => '/ping',
        'send_message' => '/message',
    ]
];