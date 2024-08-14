<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\TextMessaging\TextMessagingService;
use App\Services\TextMessaging\TextMessagingServiceInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Nutnet\LaravelSms\SmsSender;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);
        $this->app->bind(TextMessagingServiceInterface::class, static function (Application $app) {
            $sender = $app->get(SmsSender::class);

            return new TextMessagingService($sender);
        });
        $this->app->bind(\App\Adapters\Telegram\Transport\TelegramAdapterTransport::class, static function (Application $app) {
            return new \App\Adapters\Telegram\Transport\TelegramAdapterHttp();
        });
    }

    public function boot(): void
    {
        //
    }
}
