<?php

declare(strict_types=1);

namespace App\Components\Person;

use App\Common\BaseService;
use App\Models\Person;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Person::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}