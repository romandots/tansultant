<?php

declare(strict_types=1);

namespace App\Components\Dummy;

use App\Common\BaseComponentService;
use App\Models\Dummy;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Dummy::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}