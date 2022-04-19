<?php

declare(strict_types=1);

namespace App\Components\Classroom;

use App\Models\Classroom;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Classroom::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}