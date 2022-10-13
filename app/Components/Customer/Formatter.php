<?php

declare(strict_types=1);

namespace App\Components\Customer;

use App\Common\BaseFormatter;
use App\Components\Loader;

/**
 * @mixin \App\Models\Customer
 */
class Formatter extends BaseFormatter
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'person' => $this->whenLoaded('person', fn() => new \App\Components\Person\Formatter($this->person)),
            'students' => $this->whenLoaded(
                'students',
                fn() => \App\Components\Student\Formatter::collection($this->students)
            ),
            'students_count' => $this->students_count,
            'credits_sum' => Loader::credits()->getCustomerCredits($this->resource),
            'pending_bonuses_sum' => 0,//Loader::credits()->getCustomerPendingBonuses($this),
            'seen_at' => $this->created_at->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
