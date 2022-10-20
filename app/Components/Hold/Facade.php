<?php

declare(strict_types=1);

namespace App\Components\Hold;

use App\Common\BaseComponentFacade;
use App\Models\Hold;

class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function endHold(Hold $hold, \App\Models\User $user): void
    {
        $this->getService()->endHold($hold, $user);
    }
}