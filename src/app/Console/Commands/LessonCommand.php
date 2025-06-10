<?php

namespace App\Console\Commands;

use App\Components\Loader;
use Illuminate\Console\Command;

abstract class LessonCommand extends Command
{
    protected \App\Components\Lesson\Facade $lessons;

    public function __construct()
    {
        parent::__construct();
        $this->lessons = Loader::lessons();
    }
}
