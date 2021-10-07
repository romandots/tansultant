<?php

namespace App\Console\Commands;


use Carbon\Carbon;

class LessonGenerateCommand extends LessonCommand
{
    protected $signature = 'lesson:generate {date} {courseId?}';
    protected $description = 'Generates lessons of course on date';

    public function handle(): void
    {
        $date = Carbon::parse($this->argument('date'));
        $courseId = $this->argument('courseId');

        if ($courseId) {
            $this->info("Generating lessons for course #{$courseId} on {$date->toFormattedDateString()}");
            $this->lessons->generateCourseLessonsOnDate($date, $courseId);
        } else {
            $this->info("Generating lessons on {$date->toFormattedDateString()}");
            $this->lessons->generateLessonsOnDate($date);
        }

        $this->info('Done!');
    }
}
