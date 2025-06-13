<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Services\Import\ImportManager;
use App\Services\TextMessaging\TextMessagingService;
use App\Services\TextMessaging\TextMessagingServiceInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
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
        $this->app->bind(\App\Adapters\Telegram\Transport\TelegramAdapterTransport::class, static function (Application $app) {
            return new \App\Adapters\Telegram\Transport\TelegramAdapterHttp();
        });

        $this->app->singleton(ImportManager::class, function(Application $app) {
            return new ImportManager(
                DB::connection('import_source_database'),
                $app->make(LoggerInterface::class),
            );
        });
    }

    public function boot(): void
    {
        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });
    }
}
