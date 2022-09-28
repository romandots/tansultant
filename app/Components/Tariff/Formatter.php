<?php

declare(strict_types=1);

namespace App\Components\Tariff;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Tariff
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
            'courses' => $this
                ->whenLoaded('courses', fn () => \App\Components\Course\Formatter::collection($this->courses)),
            'price' => $this->price,
            'prolongation_price' => $this->prolongation_price,
            'courses_limit' => $this->courses_limit,
            'visits_limit' => $this->visits_limit,
            'days_limit' => $this->days_limit,
            'holds_limit' => $this->holds_limit,
            'status' => $this->status->value,
            'status_label' => \translate('tariff.status', $this->status),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'archived_at' => $this->archived_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
