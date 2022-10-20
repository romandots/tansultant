<?php

declare(strict_types=1);

namespace App\Components\Hold;

use App\Common\BaseComponentService;
use App\Models\Hold;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Hold::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    public function endHold(Hold $hold, \App\Models\User $user): void
    {
        $this->getRepository()->endHold($hold);
        try {
            $this->history->logEnd($user, $hold);
        } catch (\Throwable) {}
    }
}