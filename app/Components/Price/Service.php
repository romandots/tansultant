<?php

declare(strict_types=1);

namespace App\Components\Price;

use App\Common\BaseComponentService;
use App\Models\Price;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Price::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}