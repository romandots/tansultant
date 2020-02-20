<?php
declare(strict_types=1);

namespace App\Providers;

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
        \Illuminate\Auth\Events\Registered::class => [
//            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        ],
        \App\Events\UserRegisteredEvent::class => [
            //
        ],
        \App\Events\UserCreatedEvent::class => [
            //
        ],
        \App\Events\InstructorCreatedEvent::class => [
            //
        ],
        \App\Events\StudentCreatedEvent::class => [
            //
        ],

        // Courses
        \App\Events\Course\CourseCreatedEvent::class => [],
        \App\Events\Course\CourseUpdatedEvent::class => [],
        \App\Events\Course\CourseDeletedEvent::class => [],
        \App\Events\Course\CourseRecoveredEvent::class => [],
        \App\Events\Course\CourseEnabledEvent::class => [],
        \App\Events\Course\CourseDisabledEvent::class => [],
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
