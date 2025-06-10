<?php

namespace App\Components\Visit\Exceptions;

use App\Components\Loader;
use App\Components\Visit\Formatter;
use App\Exceptions\AlreadyExistsException;
use App\Models\Visit;

class VisitAlreadyExistsException extends AlreadyExistsException
{
    protected Visit $visit;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
        parent::__construct(Loader::visits()->format($visit, Formatter::class));
    }
}