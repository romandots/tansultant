<?php

declare(strict_types=1);

namespace App\Components\Bonus;

use App\Common\BaseService;
use App\Models\Bonus;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Bonus::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}