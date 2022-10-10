<?php

namespace App\Components\Visit\Entity;

use App\Models\Bonus;
use App\Models\Enum\BonusStatus;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

class PriceOptions implements Arrayable
{

    /**
     * @param float $price
     * @param Collection<Bonus> $bonuses
     */
    public function __construct(
        public readonly float $price,
        public readonly Collection $bonuses
    ) { }

    #[ArrayShape(['price' => "float", 'bonuses' => "array"])] public function toArray(): array
    {

        $pricesWithBonuses = [];
        foreach ($this->bonuses as $bonus) {
            if ($bonus->status !== BonusStatus::PENDING) {
                continue;
            }

            $discountPrice = $this->price - $bonus->amount;
            // here we can control the maximum size of bonus discount
            if ($discountPrice < 0) {
                continue;
            }

            $pricesWithBonuses[] = [
                'price' => $discountPrice,
                'bonus_id' => $bonus->id,
                'bonus_name' => $bonus->name,
                'bonus_amount' => $bonus->amount,
                'bonus_expired_at' => $bonus->expired_at->toDateTimeString(),
            ];
        }

        return [
            'price' => $this->price,
            'bonuses' => $pricesWithBonuses,
        ];
    }
}