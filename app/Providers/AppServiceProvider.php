<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\TextMessaging\TextMessagingService;
use App\Services\TextMessaging\TextMessagingServiceInterface;
use Faker\Generator;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Nutnet\LaravelSms\SmsSender;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TextMessagingServiceInterface::class, static function (Application $app) {
            $sender = $app->get(SmsSender::class);

            return new TextMessagingService($sender);
        });
    }

    public function boot(): void
    {
        //
    }
}
