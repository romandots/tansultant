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

/**
 * @method Repository getRepository()
 */
class Validator extends BaseComponentService
{
    protected const ACTION_UPDATE = 'update';
    protected const ACTION_PROLONG = 'prolong';
    protected const ACTION_UNHOLD = 'unhold';
    protected const ACTION_DELETE = 'delete';
    protected const ACTION_ATTACH_COURSES = 'attach_courses';
    protected const ACTION_DETACH_COURSES = 'detach_courses';

    public function __construct()
    {
        parent::__construct(
            Subscription::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    public function getAllowedSources(SubscriptionStatus $toStatus): array
    {
        $allowedStatuses = [];
        foreach (SubscriptionStatus::cases() as $subscriptionStatus) {
            $allowedDestinations = $this->getAllowedDestinations($subscriptionStatus);
            if (\in_array($toStatus, $allowedDestinations, true)) {
                $allowedStatuses = [$subscriptionStatus];
            }
        }
        return $allowedStatuses;
    }

    public function getAllowedDestinations(SubscriptionStatus $fromStatus): array
    {
        return match ($fromStatus) {
            SubscriptionStatus::NOT_PAID => [
                SubscriptionStatus::PENDING,
                SubscriptionStatus::CANCELED,
            ],
            SubscriptionStatus::PENDING => [
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::CANCELED,
            ],
            SubscriptionStatus::ACTIVE => [
                SubscriptionStatus::ON_HOLD,
                SubscriptionStatus::EXPIRED,
                SubscriptionStatus::CANCELED,
            ],
            SubscriptionStatus::ON_HOLD => [
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::CANCELED,
            ],
            SubscriptionStatus::EXPIRED => [
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::CANCELED,
            ],
            SubscriptionStatus::CANCELED => [],
        };
    }

    public function getAllowedStatusesForAction(string $action): array
    {
        return match ($action) {
            self::ACTION_ATTACH_COURSES, self::ACTION_DETACH_COURSES => [
                SubscriptionStatus::PENDING,
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::ON_HOLD,
            ],
            self::ACTION_UPDATE => [
                SubscriptionStatus::NOT_PAID,
            ],
            self::ACTION_PROLONG => [
                SubscriptionStatus::PENDING,
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::EXPIRED,
            ],
            self::ACTION_UNHOLD => [
                SubscriptionStatus::ON_HOLD,
            ],
            self::ACTION_DELETE => [
                SubscriptionStatus::CANCELED,
            ],
        };
    }

    public function canTransit(SubscriptionStatus $fromStatus, SubscriptionStatus $toStatus): bool
    {
        $allowedSources = $this->getAllowedDestinations($fromStatus);
        return \in_array($toStatus, $allowedSources, true);
    }

    public function canDo(SubscriptionStatus $fromStatus, string $action): bool
    {
        $allowedStatuses = $this->getAllowedStatusesForAction($action);
        return \in_array($fromStatus, $allowedStatuses, true);
    }

    public function canBeUpdated(Subscription $subscription): bool
    {
        return $this->canDo($subscription->status, self::ACTION_UPDATE);
    }

    public function canBePaused(Subscription $subscription): bool
    {
        $subscription->load('holds');
        return $this->canTransit($subscription->status, SubscriptionStatus::ON_HOLD)
            && ($subscription->holds_left === null || $subscription->holds_left > 0);
    }

    public function canBeUnpaused(Subscription $subscription): bool
    {
        return $this->canDo($subscription->status, self::ACTION_UNHOLD);
    }

    public function canAttachCourses(Subscription $subscription): bool
    {
        return $this->canDo($subscription->status, self::ACTION_ATTACH_COURSES);
    }

    public function canDetachCourses(Subscription $subscription): bool
    {
        return $this->canDo($subscription->status, self::ACTION_DETACH_COURSES);
    }

    public function canBeProlonged(Subscription $subscription): bool
    {
        return $this->canDo($subscription->status, self::ACTION_PROLONG)
            || (SubscriptionStatus::EXPIRED === $subscription->status && $subscription->canBeProlongated());
    }

    public function canBeDeleted(Subscription $subscription): bool
    {
        return $this->canDo($subscription->status, self::ACTION_DELETE);
    }

    public function canBeCanceled(Subscription $subscription): bool
    {
        return $this->canTransit($subscription->status, SubscriptionStatus::CANCELED);
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

    public function validateBonus(?\App\Models\Bonus $bonus): void
    {
        if (null === $bonus || $bonus->status === BonusStatus::PENDING) {
            return;
        }

        throw new InvalidBonusStatus($bonus->status, [BonusStatus::PENDING]);
    }

    public function validateSubscriptionStatusForUpdate(Subscription $subscription): void
    {
        if (!$this->canBeUpdated($subscription)) {
            throw new Exceptions\InvalidSubscriptionStatus(
                $subscription->status->value, $this->getAllowedStatusesForAction(self::ACTION_UPDATE)
            );
        }
    }

    public function validateSubscriptionStatusForHold(Subscription $subscription): void
    {
        if (!$this->canBePaused($subscription)) {

            if ($this->canTransit($subscription->status, SubscriptionStatus::ON_HOLD)) {
                throw new Exceptions\NoHoldsAvailable($subscription);
            }

            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    public function validateSubscriptionStatusForUnhold(Subscription $subscription): void
    {
        if (!$this->canBeUnpaused($subscription)) {
            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    public function validateSubscriptionStatusForProlong(Subscription $subscription): void
    {
        if (!$this->canBeProlonged($subscription)) {
            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    public function validateSubscriptionStatusForDelete(Subscription $subscription): void
    {
        if (!$this->canBeDeleted($subscription)) {
            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    public function validateSubscriptionStatusForCancel(Subscription $subscription): void
    {
        if (!$this->canBeCanceled($subscription)) {
            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    public function validateSubscriptionStatusForAttachCourses(Subscription $subscription): void
    {
        if (!$this->canAttachCourses($subscription)) {
            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    public function validateSubscriptionStatusForDetachCourses(Subscription $subscription): void
    {
        if (!$this->canDetachCourses($subscription)) {
            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    public function validateSubscriptionStatusForTransition(Subscription $subscription, SubscriptionStatus $toStatus): void
    {
        if (!$this->canTransit($subscription->status, $toStatus)) {
            $this->throwInvalidSubscriptionStatusException($subscription);
        }
    }

    protected function throwInvalidSubscriptionStatusException(Subscription $subscription): void
    {
        throw new Exceptions\InvalidSubscriptionStatus(
            $subscription->status->value,
            $this->getAllowedDestinations($subscription->status)
        );
    }
}