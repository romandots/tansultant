<?php

declare(strict_types=1);

namespace App\Components\Person;

use App\Models\Person;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
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