<?php

declare(strict_types=1);

namespace App\Components\Formula;

use App\Common\BaseComponentFacade;
use App\Models\Formula;

class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function describeFormulaEquation(?string $formulaEquation): string
    {
        return $this->getService()->describeFormulaEquation($formulaEquation);
    }

    public function calculateLessonPayoutAmount(\App\Models\Lesson $lesson, Formula $formula): int
    {
        return (int)$this->getService()->calculateLessonPayoutAmount($lesson, $formula);
    }
}