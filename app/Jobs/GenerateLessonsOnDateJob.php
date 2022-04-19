<?php

namespace App\Jobs;

use App\Components\Lesson\Facade;
use App\Services\WithLogger;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLessonsOnDateJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WithLogger;

    private Carbon $date;

    public int $uniqueFor = 60;

    public function __construct(Carbon $date)
    {
        $this->date = $date;
    }

    /**
     * The unique ID of the job.
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->date->toDateString();
    }

    public function handle(Facade $lessons): void
    {
        $this->debug('Handling GenerateLessonsOnDate job for date: ' . $this->date->toDateString());
        $lessons->generateLessonsOnDate($this->date);
    }
}
