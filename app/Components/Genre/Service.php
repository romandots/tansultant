<?php

declare(strict_types=1);

namespace App\Components\Genre;

use App\Common\BaseService;
use App\Models\Genre;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Genre::class,
            Repository::class,
            Dto::class,
            null
        );
    }
}