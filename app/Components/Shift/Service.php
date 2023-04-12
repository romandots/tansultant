<?php

declare(strict_types=1);

namespace App\Components\Shift;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Components\Loader;
use App\Events\User\UserEvent;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Shift::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Dto $dto
     * @return Shift
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $user = $dto->getUser();
        if (null === $user) {
            throw new \LogicException('user_is_not_defined');
        }

        if (null !== $user->active_shift) {
            throw new Exceptions\UserAlreadyHasActiveShift($user);
        }

        $dto->name = trans('shift.name', [
            'user' => (string)$dto->getUser(),
            'date' => Carbon::now()->format('l, j F Y'),
        ]);

        $shift = \DB::transaction(function () use ($dto, $user) {
            /** @var Shift $shift */
            $shift = parent::create($dto);
            Loader::users()->getRepository()->attachActiveShift($user, $shift);

            return $shift;
        });

        $this->dispatchOpenedEvent($shift);

        return $shift;
    }

    public function getUserActiveShift(\App\Models\User $user): Shift
    {
        if (null === $user->active_shift) {
            throw new Exceptions\UserHasNoActiveShift($user);
        }

        return $user->active_shift()->with('branch')->first();
    }

    public function closeUserActiveShift(\App\Models\User $user): Shift
    {
        if (null === $user->active_shift) {
            throw new Exceptions\UserHasNoActiveShift($user);
        }

        $shift = $user->active_shift;

        // @todo Sum up all transactions and divide them to categories and types

        $totalBalance = 0;

        try {
            \DB::transaction(function () use ($totalBalance, $user, $shift) {
                $this->getRepository()->close($shift, $totalBalance);
                Loader::users()->getRepository()->detachActiveShift($user);
                $this->debug('Shift ' . $shift . ' closed');
            });
        } catch (\Throwable $exception) {
            $this->error('Failed closing shift ' . $shift->name);
            throw $exception;
        }

        try {
            $this->history->logClose($user, $shift);
        } catch (\Throwable) { }

        $this->dispatchClosedEvent($shift);

        return $shift;
    }

    protected function dispatchOpenedEvent(Shift $shift): void
    {
        try {
            UserEvent::shiftOpened($shift);
        } catch (\Throwable) {}
    }

    protected function dispatchClosedEvent(Shift $shift): void
    {
        try {
            UserEvent::shiftClosed($shift);
        } catch (\Throwable) {}
    }
}