<?php

namespace App\Providers;

use App\Checks\ReverbCheck;
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
            ReverbCheck::new((int)config('reverb.servers.reverb.port', 8080)),
            HorizonCheck::new(),
            //QueueCheck::new(),
            QueueSizeCheck::new(),
            ScheduleCheck::new(),
            PingCheck::new()->url(config('app.url'))->name('Front App'),
            PingCheck::new()->url(config('telegram.api_host') . config('telegram.endpoints.ping'))->name('Telegram Server'),
            UsedDiskSpaceCheck::new(),
            RedisMemoryUsageCheck::new(),
            EnvironmentCheck::new(),
        ]);
    }
}
