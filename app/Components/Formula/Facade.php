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
}