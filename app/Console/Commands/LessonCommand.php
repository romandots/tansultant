<?php

namespace App\Console\Commands;

use App\Services\Lesson\LessonFacade;
use Illuminate\Console\Command;

abstract class LessonCommand extends Command
{
    protected LessonFacade $lessons;

    public function __construct(LessonFacade $lessons)
    {
        parent::__construct();
        $this->lessons = $lessons;
    }
}
