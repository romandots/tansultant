<?php

declare(strict_types=1);

namespace App\Components\Shift;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Components\Loader;
use App\Events\User\UserEvent;
use App\Models\Shift;
use App\Services\Permissions\ShiftsPermission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $dto->name = $this->generateShiftName($dto);

        $shift = \DB::transaction(function () use ($dto, $user) {
            /** @var Shift $shift */
            $shift = parent::create($dto);
            Loader::users()->getRepository()->attachActiveShift($user, $shift);

            return $shift;
        });

        $this->dispatchOpenedEvent($shift);

        return $shift;
    }

    /**
     * @param Shift $shift
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection|Transaction[]
     */
    public function getShiftTransactions(Shift $shift, \App\Models\User $user): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->canUserSeeTheShift($user, $shift)) {
            throw new NotFoundHttpException('Shift not found');
        }

        return $shift->load('transactions')
            ->transactions()
            ->with(['account', 'customer', 'user', ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserActiveShift(\App\Models\User $user): Shift
    {
        if (null === $user->active_shift) {
            throw new Exceptions\UserHasNoActiveShift($user);
        }

        return $user->active_shift()->with('branch')->first();
    }

    public function closeShift(Shift $shift, \App\Models\User $user): void
    {
        try {
            \DB::transaction(function () use ($user, $shift) {
                $this->getRepository()->updateTotalIncomeAndClose($shift);
                Loader::users()->getRepository()->detachActiveShift($user);
                $this->debug('Shift ' . $shift . ' closed');
            });
        } catch (\Throwable $exception) {
            $this->error('Failed closing shift ' . $shift->name);
            throw $exception;
        }

        try {
            $this->history->logClose($user, $shift);
        } catch (\Throwable) {
        }

        $this->dispatchClosedEvent($shift);
    }

    public function closeUserActiveShift(\App\Models\User $user): Shift
    {
        $shift = $user->active_shift;
        if (null === $shift) {
            throw new Exceptions\UserHasNoActiveShift($user);
        }

        $this->closeShift($shift, $user);

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

    protected function canUserSeeTheShift(\App\Models\User $user, Shift $shift): bool
    {
        return (
            $user->can(ShiftsPermission::MANAGE) ||
            $user->can(ShiftsPermission::READ) ||
            ($user->can(ShiftsPermission::READ_OWN) && $user->id === $shift->user_id)
        );
    }

    protected function generateShiftName(Dto $dto): string
    {
        return trans('shift.name', [
            'user' => (string)$dto->getUser(),
            'date' => Carbon::now()->format('d.m.Y, H:i'),
        ]);
    }

    public function updateTotalIncome(Shift $shift): void
    {
        $this->getRepository()->updateTotalIncome($shift);
    }
}