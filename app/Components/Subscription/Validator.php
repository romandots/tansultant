<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentService;
use App\Components\Bonus\Exceptions\InvalidBonusStatus;
use App\Models\Enum\BonusStatus;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Enum\TariffStatus;
use App\Models\Subscription;
use App\Models\Tariff;
use Carbon\Carbon;

/**
 * @method Repository getRepository()
 */
class Validator extends BaseComponentService
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

    public function validateTariff(?Tariff $tariff): void
    {
        if (null === $tariff || $tariff->deleted_at || TariffStatus::ACTIVE !== $tariff->status) {
            throw new Exceptions\TariffIsNoLongerActive($tariff);
        }
    }

    public function validateStudent(\App\Models\Student $student): void
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
    public function validateSubscriptionStatusForHold(Subscription $subscription): void
    {
        $allowedStatuses = [SubscriptionStatus::ACTIVE];
        if (!\in_array($subscription->status, $allowedStatuses, true)) {
            throw new Exceptions\InvalidSubscriptionStatus($subscription->status->value, $allowedStatuses);
        }
    }

    /**
     * @param Subscription $subscription
     * @return void
     */
    public function validateSubscriptionStatusForUnhold(Subscription $subscription): void
    {
        $allowedStatuses = [SubscriptionStatus::ON_HOLD];
        if (!\in_array($subscription->status, $allowedStatuses, true)) {
            throw new Exceptions\InvalidSubscriptionStatus($subscription->status->value, $allowedStatuses);
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

        $prolongationPeriod = \config('subscriptions.prolongation_extra_period', 0);
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

    public function validateBonus(?\App\Models\Bonus $bonus): void
    {
        if (null === $bonus || $bonus->status === BonusStatus::PENDING) {
            return;
        }

        throw new InvalidBonusStatus($bonus->status, [BonusStatus::PENDING]);
    }
}