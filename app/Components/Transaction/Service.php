<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Common\Contracts;
use App\Components\Shift\Exceptions\UserHasNoActiveShift;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    protected \App\Components\Account\Facade $accounts;

    public function __construct()
    {
        parent::__construct(
            Transaction::class,
            Repository::class,
            Dto::class,
            null
        );
        $this->accounts = \app(\App\Components\Account\Facade::class);
    }

    /**
     * @param Dto $dto
     * @return Transaction
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $user = $dto->getUser();
        if (null === $user) {
            throw new \LogicException('user_is_not_defined');
        }

        $shift = $user?->load('active_shift')->active_shift;
        if (null === $shift) {
            throw new UserHasNoActiveShift($user);
        }

        $dto->shift_id = $shift->id;

        return parent::create($dto);
    }


}