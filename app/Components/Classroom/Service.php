<?php

declare(strict_types=1);

namespace App\Components\Classroom;

use App\Common\BaseService;
use App\Models\Classroom;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
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