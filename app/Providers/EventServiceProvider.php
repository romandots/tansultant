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
//        \Illuminate\Auth\Events\Registered::class => [
//            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
//        ],

        // Users
        \App\Events\UserRegisteredEvent::class => [],
        \App\Events\UserCreatedEvent::class => [],

        // Instructors
        \App\Events\InstructorCreatedEvent::class => [],

        // Students
        \App\Events\StudentCreatedEvent::class => [],

        // Courses
        \App\Events\CourseCreatedEvent::class => [],
        \App\Events\CourseEnabledEvent::class => [],
        \App\Events\CourseDisabledEvent::class => [],
        \App\Events\CourseDeletedEvent::class => [],
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
