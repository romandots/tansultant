<?php

namespace App\Console;

use App\Console\Commands\LessonGenerateCommand;
use App\Console\Commands\LessonUpdateCommand;
use App\Console\Commands\StudentCreditAdd;
use App\Console\Commands\SubscriptionUpdateCommand;
use App\Console\Commands\UserCreateCommand;
use App\Console\Commands\UserStatusUpdateCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UserCreateCommand::class,
        UserStatusUpdateCommand::class,
        LessonGenerateCommand::class,
        LessonUpdateCommand::class,
        SubscriptionUpdateCommand::class,
        StudentCreditAdd::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule
            ->command(LessonUpdateCommand::class)
            ->everyMinute();
        $schedule
            ->command(SubscriptionUpdateCommand::class)
            ->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
