<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Common\Contracts\DtoWithUser;
use App\Components\Loader;
use App\Events\Subscription\SubscriptionUpdatedEvent;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Subscription;
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
        $bonus = $dto->bonus_id ? Loader::bonuses()->find($dto->bonus_id) : null;

        $this->getValidator()->validateTariff($tariff);
        $this->getValidator()->validateStudent($student);
        $this->getValidator()->validateBonus($bonus);

        $dto->name = $tariff->name;
        $dto->status = SubscriptionStatus::NOT_PAID;
        $this->addTariffValues($dto, $tariff);

        return \DB::transaction(function () use ($bonus, $student, $dto) {
            /** @var Subscription $subscription */
            $subscription = parent::create($dto);
            $payment = Loader::payments()->createSubscriptionPayment($subscription, $student, $bonus, $dto->getUser());
            $this->getRepository()->attachPayment($subscription, $payment);

            return $subscription;
        });
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

    public function addTariffValues(Subscription|Dto $subscription, \App\Models\Tariff $tariff): void
    {
        $subscription->courses_limit = $tariff->courses_limit;
        $subscription->days_limit = $tariff->days_limit === null ? null : $subscription->days_limit + $tariff->days_limit;
        $subscription->visits_limit = $tariff->visits_limit === null ? null : $subscription->visits_limit + $tariff->visits_limit;
        $subscription->holds_limit = $tariff->holds_limit === null ? null : $subscription->holds_limit + $tariff->holds_limit;
    }

    public function dispatchEvents(iterable $subscriptions): void
    {
        foreach ($subscriptions as $subscription) {
            assert($subscription instanceof Subscription);
            try {
                $this->debug(
                    "Dispatching SubscriptionUpdatedEvent for subscription #{$subscription->id}"
                );
                SubscriptionUpdatedEvent::dispatch($subscription);
            } catch (\Exception $exception) {
                $this->error('Failed dispatching SubscriptionUpdatedEvent', $exception);
            }
        }
    }

    protected function getValidator(): Validator
    {
        return app(Validator::class);
    }
}