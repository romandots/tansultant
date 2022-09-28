<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Common\Contracts\DtoWithUser;
use App\Components\Loader;
use App\Models\Course;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Enum\TariffStatus;
use App\Models\Subscription;
use App\Models\Tariff;
use App\Models\User;
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
            Subscription::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Dto $dto
     * @return Subscription
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $tariff = Loader::tariffs()->find($dto->tariff_id);
        $student = Loader::students()->find($dto->student_id);

        $this->validateTariff($tariff);
        $this->validateStudent($student);

        $dto->name = $tariff->name;
        $dto->status = SubscriptionStatus::NOT_PAID;
        $this->addTariffValues($dto, $tariff);

        return parent::create($dto);
    }


    /**
     * @param Subscription $record
     * @param Dto $dto
     * @return void
     * @throws \Throwable
     * @throws Exceptions\InvalidSubscriptionStatus
     */
    public function update(Model $record, DtoWithUser $dto): void
    {
        $changingTariff = $record->tariff_id !== $dto->tariff_id;

        if ($changingTariff) {
            $this->validateSubscriptionStatusForUpdate($record);
        }

        $tariff = Loader::tariffs()->find($dto->tariff_id);
        $student = Loader::students()->find($dto->student_id);

        $this->validateTariff($tariff);
        $this->validateStudent($student);

        $dto->name = $tariff->name;
        $dto->status = $record->status;

        if ($changingTariff) {
            $this->addTariffValues($dto, $tariff);
        } else {
            $dto->days_limit = $record->days_limit;
            $dto->courses_limit = $record->courses_limit;
            $dto->visits_limit = $record->visits_limit;
            $dto->holds_limit = $record->holds_limit;
        }

        parent::update($record, $dto);
    }

    /**
     * @param Subscription $record
     * @param \App\Models\User $user
     * @return void
     * @throws \Throwable
     * @throws Exceptions\InvalidSubscriptionStatus
     */
    public function delete(Model $record, \App\Models\User $user): void
    {
        $this->validateSubscriptionStatusForDelete($record);

        parent::delete($record, $user);
    }

    public function prolong(Subscription $subscription, User $user): void
    {
        $this->validateSubscriptionStatusForProlong($subscription);

        $tariff = $subscription->tariff;
        $this->validateTariff($tariff);

        $originalRecord = clone $subscription;

        $this->addTariffValues($subscription, $tariff);
        $this->setExpirationDate($subscription);

        \DB::transaction(function () use ($subscription, $originalRecord, $user) {
            $this->getRepository()->save($subscription);
            $this->history->logUpdate($user, $subscription, $originalRecord);

            $this->debug("Prolong subscription #{$subscription->id}");
        });
    }

    public function activate(Subscription $subscription): void
    {
        $this->validateSubscriptionStatusForActivate($subscription);
        $this->getRepository()->updateStatus($subscription, SubscriptionStatus::ACTIVE);
        $this->debug("Activated subscription #{$subscription->id}");
    }

    private function getExtraDays(): int
    {
        return (int)\config('subscriptions.prolongation_extra_period', 0);
    }

    private function addTariffValues(Subscription|Dto $subscription, \App\Models\Tariff $tariff): void
    {
        $subscription->courses_limit = $tariff->courses_limit;
        $subscription->days_limit = $tariff->days_limit === null ? null : $subscription->days_limit + $tariff->days_limit;
        $subscription->visits_limit = $tariff->visits_limit === null ? null : $subscription->visits_limit + $tariff->visits_limit;
        $subscription->holds_limit = $tariff->holds_limit === null ? null : $subscription->holds_limit + $tariff->holds_limit;
    }

    private function setExpirationDate(Subscription $subscription, bool $save = false): void
    {
        if (!$subscription->activated_at) {
            return;
        }

        $days = $subscription->tariff->days_limit ?? 3650;
        $subscription->expired_at = $subscription->activated_at->clone()
            ->addDays($days)
            ->setHour(23)
            ->setMinute(59)
            ->setSecond(59);

        // @todo add holds periods

        if ($save) {
            $this->getRepository()->save($subscription);
        }
    }

    /**
     * @param Subscription $subscription
     * @param iterable<Course> $courses
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function attachCourses(Subscription $subscription, iterable $courses, User $user): void
    {
        $originalRecord = clone $subscription;
        $this->getRepository()->attachCourses($subscription, $courses);
        $this->debug("Attach courses to subscription {$subscription->name}", (array)$courses);
        $this->history->logUpdate($user, $subscription, $originalRecord);
    }

    /**
     * @param Subscription $subscription
     * @param iterable<Course> $courses
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function detachCourses(Subscription $subscription, iterable $courses, User $user): void
    {
        $originalRecord = clone $subscription;
        $this->getRepository()->detachCourses($subscription, $courses);
        $this->debug("Detach courses subscription tariff {$subscription->name}", (array)$courses);
        $this->history->logUpdate($user, $subscription, $originalRecord);
    }

    private function validateTariff(?Tariff $tariff): void
    {
        if (null === $tariff || $tariff->deleted_at || TariffStatus::ACTIVE !== $tariff->status) {
            throw new Exceptions\TariffIsNoLongerActive($tariff);
        }
    }

    private function validateStudent(\App\Models\Student $student): void
    {
    }

    /**
     * @param Subscription $record
     * @return void
     */
    public function validateSubscriptionStatusForUpdate(Subscription $record): void
    {
        $allowedStatuses = [SubscriptionStatus::NOT_PAID];
        if (!in_array($record->status, $allowedStatuses, true)) {
            throw new Exceptions\InvalidSubscriptionStatus($record->status->value, $allowedStatuses);
        }
    }

    /**
     * @param Subscription $subscription
     * @return void
     */
    public function validateSubscriptionStatusForProlong(Subscription $subscription): void
    {
        $allowedStatuses = [SubscriptionStatus::ACTIVE, SubscriptionStatus::EXPIRED, SubscriptionStatus::ON_HOLD];
        if (!\in_array($subscription->status, $allowedStatuses, true)) {
            throw new Exceptions\InvalidSubscriptionStatus($subscription->status->value, $allowedStatuses);
        }

        $prolongationPeriod = $this->getExtraDays();
        if (SubscriptionStatus::EXPIRED === $subscription->status
            && Carbon::now()->diffInDays($subscription->expired_at) > $prolongationPeriod) {
            throw new Exceptions\ProlongationPeriodExpired($subscription->expired_at, $prolongationPeriod);
        }
    }

    /**
     * @param Subscription $record
     * @return void
     */
    public function validateSubscriptionStatusForDelete(Subscription $record): void
    {
        $allowedStatuses = [SubscriptionStatus::EXPIRED, SubscriptionStatus::CANCELED, SubscriptionStatus::NOT_PAID];
        if (!\in_array($record->status, $allowedStatuses, true)) {
            throw new Exceptions\InvalidSubscriptionStatus($record->status->value, $allowedStatuses);
        }
    }

    private function validateSubscriptionStatusForActivate(Subscription $record): void
    {
        $allowedStatuses = [SubscriptionStatus::PENDING];
        if (!\in_array($record->status, $allowedStatuses, true)) {
            throw new Exceptions\InvalidSubscriptionStatus($record->status->value, $allowedStatuses);
        }
    }
}