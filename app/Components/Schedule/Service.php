<?php

declare(strict_types=1);

namespace App\Components\Schedule;

use App\Models\Schedule;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Schedule::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}