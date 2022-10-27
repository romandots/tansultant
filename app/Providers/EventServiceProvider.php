<?php
declare(strict_types=1);

namespace App\Providers;

use App\Listeners\UpdateStudentLastSeen;
use App\Listeners\UpdateStudentPendingStatus;
use App\Listeners\UpdateSubscriptionHoldStatus;
use App\Listeners\UpdateSubscriptionPendingStatus;
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
        \App\Events\User\UserRegisteredEvent::class => [
            //
        ],
        \App\Events\User\UserCreatedEvent::class => [
            //
        ],
        \App\Events\User\UserShiftOpenedEvent::class => [
            //
        ],
        \App\Events\User\UserShiftClosedEvent::class => [
            //
        ],
        \App\Events\Instructor\InstructorCreatedEvent::class => [
            //
        ],
        \App\Events\Student\StudentCreatedEvent::class => [
            //
        ],

        // Courses
        \App\Events\Course\CourseCreatedEvent::class => [],
        \App\Events\Course\CourseUpdatedEvent::class => [],
        \App\Events\Course\CourseDeletedEvent::class => [],
        \App\Events\Course\CourseRestoredEvent::class => [],
        \App\Events\Course\CourseEnabledEvent::class => [],
        \App\Events\Course\CourseDisabledEvent::class => [],

        // Holds
        \App\Events\Hold\HoldCreatedEvent::class => [
            UpdateSubscriptionHoldStatus::class,
        ],
        \App\Events\Hold\HoldEndedEvent::class => [
            UpdateSubscriptionHoldStatus::class,
        ],
        \App\Events\Hold\HoldDeletedEvent::class => [
            UpdateSubscriptionHoldStatus::class,
        ],

        // Visits
        \App\Events\Visit\VisitCreatedEvent::class => [
            UpdateStudentPendingStatus::class,
            UpdateStudentLastSeen::class,
            UpdateSubscriptionPendingStatus::class,
        ],
        \App\Events\Visit\VisitDeletedEvent::class => [
            UpdateStudentPendingStatus::class,
            UpdateStudentLastSeen::class,
            UpdateSubscriptionPendingStatus::class,
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
