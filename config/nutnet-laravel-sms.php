<?php
declare(strict_types=1);
/**
 * @author Maksim Khodyrev<maximkou@gmail.com>
 * 17.07.17
 */
return [
    /**
     * название класса-провайдера
     * Доступные провайдеры:
     * * \Nutnet\LaravelSms\Providers\Log (alias: log)
     * * \Nutnet\LaravelSms\Providers\Smpp (alias: smpp)
     * * \Nutnet\LaravelSms\Providers\SmscRu (alias: smscru)
     * * \Nutnet\LaravelSms\Providers\SmsRu (alias: smsru)
     * * \Nutnet\LaravelSms\Providers\IqSmsRu (alias: iqsmsru)
     * @see Nutnet\LaravelSms\Providers
     */
    'provider' => env('SMS_PROVIDER', 'log'),

    /**
     * настройки, специфичные для провайдера
     */
    'provider_options' => [
        'auth_type' => 'standard',
        'login' => \env('SMS_LOGIN'),
        'password' => \env('SMS_PASSWORD'),
        'api_id' => \env('SMS_API_ID'),
        'partner_id' => \env('SMS_PARTNER_ID'),
    ],
];
