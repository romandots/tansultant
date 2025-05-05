<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\Import\ImportManager;
use App\Services\TextMessaging\TextMessagingService;
use App\Services\TextMessaging\TextMessagingServiceInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Nutnet\LaravelSms\SmsSender;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);
        $this->app->bind(TextMessagingServiceInterface::class, static function (Application $app) {
            $sender = $app->get(SmsSender::class);

            return new TextMessagingService($sender);
        });

        $this->app->singleton(ImportManager::class, function(Application $app) {
            return new ImportManager(
                DB::connection('old_database'),
                $app->make(LoggerInterface::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
