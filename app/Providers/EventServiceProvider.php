<?php
declare(strict_types=1);

namespace App\Providers;

use App\Events\UserRegisteredEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserRegisteredEvent::class => [
            // log this event
        ],
    ];

    /**
     * Register any events for your application.
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
        //
    }
}
