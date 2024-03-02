<?php

namespace App\Providers;

use App\Checks\WebsocketCheck;
use Illuminate\Support\ServiceProvider;
use QueueSizeCheck\QueueSizeCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Health::checks([
            DatabaseCheck::new(),
            RedisCheck::new(),
            CacheCheck::new(),
            WebsocketCheck::new((int)config('websockets.dashboard.port', 6001)),
            HorizonCheck::new(),
            //QueueCheck::new(),
            QueueSizeCheck::new(),
            ScheduleCheck::new(),
            PingCheck::new()->url(config('app.url'))->name('Front App'),
            UsedDiskSpaceCheck::new(),
            RedisMemoryUsageCheck::new(),
            EnvironmentCheck::new(),
        ]);
    }
}
