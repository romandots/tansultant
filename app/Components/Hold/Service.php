<?php

declare(strict_types=1);

namespace App\Components\Hold;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Events\Hold\HoldEvent;
use App\Models\Hold;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * @param Dto $dto
     * @return Hold
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $hold = parent::create($dto);
        assert($hold instanceof  Hold);
        HoldEvent::created($hold, $dto->user);
        return $hold;
    }

    /**
     * @param Hold $record
     * @param \App\Models\User $user
     * @return void
     * @throws \Throwable
     */
    public function delete(Model $record, \App\Models\User $user): void
    {
        parent::delete($record, $user);
        HoldEvent::deleted($record, $user);
    }


    public function endHold(Hold $hold, \App\Models\User $user): void
    {
        $this->getRepository()->endHold($hold);
        HoldEvent::ended($hold, $user);
        try {
            $this->history->logEnd($user, $hold);
        } catch (\Throwable) {}
    }
}