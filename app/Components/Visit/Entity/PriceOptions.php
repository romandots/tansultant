<?php

namespace App\Components\Visit\Entity;

use App\Models\Bonus;
use App\Models\Enum\BonusStatus;
use App\Models\Subscription;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

class PriceOptions implements Arrayable
{
    public function __construct(
        public readonly int $price,
        public readonly Collection $bonuses,
        public ?Collection $subscriptionsWithCourse = null,
        public ?Collection $subscriptionsWithoutCourse = null,
    ) { }

    public function toArray(): array
    {
        $pricesWithBonuses = [];
        foreach ($this->bonuses as $bonus) {
            if ($bonus->status !== BonusStatus::PENDING) {
                continue;
            }

            $discountPrice = (int)$this->price - (int)$bonus->amount;
            // here we can control the maximum size of bonus discount
            if ($discountPrice < 0) {
                continue;
            }

            $pricesWithBonuses[] = $this->formatBonus($bonus, $discountPrice);
        }

        $subscriptionsWithCourse = [];
        if ($this->subscriptionsWithCourse) {
            foreach ($this->subscriptionsWithCourse as $subscription) {
                $subscriptionsWithCourse[] = $this->formatSubscription($subscription, true);
            }
        }

        $subscriptionsWithoutCourse = [];
        if ($this->subscriptionsWithoutCourse) {
            foreach ($this->subscriptionsWithoutCourse as $subscription) {
                $subscriptionsWithoutCourse[] = $this->formatSubscription($subscription, false);
            }
        }

        return [
            ['price' => $this->price],
            ...$pricesWithBonuses,
            ...$subscriptionsWithCourse,
            ...$subscriptionsWithoutCourse,
        ];
    }

    #[ArrayShape([
        'price' => "int",
        'bonus_id' => "string",
        'bonus_name' => "string",
        'bonus_amount' => "int",
        'bonus_expired_at' => "string"
    ])] protected function formatBonus(Bonus $bonus, int $price): array
    {
        return [
            'price' => $price,
            'bonus_id' => $bonus->id,
            'bonus_name' => $bonus->name,
            'bonus_amount' => $bonus->amount,
            'bonus_expired_at' => $bonus->expired_at->toDateTimeString(),
        ];
    }

    #[ArrayShape([
        'price' => "int",
        'subscription_id' => "string",
        'subscription_name' => "string",
        'subscription_visits_left' => "int|null",
        'subscription_expired_at' => "string",
        'subscription_has_course' => "bool"
    ])] protected function formatSubscription(Subscription $subscription, bool $hasCourse): array
    {
        return [
            'price' => 0,
            'subscription_id' => $subscription->id,
            'subscription_name' => $subscription->name,
            'subscription_visits_left' => $subscription->visits_left,
            'subscription_expired_at' => $subscription->expired_at?->toDateTimeString(),
            'subscription_has_course' => $hasCourse,
        ];
    }
}