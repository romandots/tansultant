<?php

declare(strict_types=1);

namespace App\Components\Formula;

use App\Common\BaseComponentFacade;

class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function describeEquation(?string $equation): string
    {
        return $this->getService()->describeEquation($equation);
    }

    public function calculateLessonPayoutAmount(\App\Models\Lesson $lesson, \App\Models\Formula $formula): int
    {
        return (int)$this->getService()->calculateLessonPayoutAmount($lesson, $formula);
    }
}