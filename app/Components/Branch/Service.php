<?php

declare(strict_types=1);

namespace App\Components\Branch;

use App\Models\Branch;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Branch::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}