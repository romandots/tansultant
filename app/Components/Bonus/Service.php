<?php

declare(strict_types=1);

namespace App\Components\Bonus;

use App\Models\Bonus;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
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

    public function activateBonus(Bonus $bonus): void
    {
        $this->getRepository()->setStatusActivated($bonus);
    }

    public function cancelBonus(Bonus $bonus): void
    {
        $this->getRepository()->setStatusCanceled($bonus);
    }

    public function expireBonus(Bonus $bonus): void
    {
        $this->getRepository()->setStatusExpired($bonus);
    }
}