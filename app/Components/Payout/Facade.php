<?php

declare(strict_types=1);

namespace App\Components\Payout;

use App\Common\BaseComponentFacade;

class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }
}