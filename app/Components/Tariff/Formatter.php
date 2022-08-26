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
            'courses_count' => $this->courses_count,
            'visits_count' => $this->visits_count,
            'days_count' => $this->days_count,
            'holds_count' => $this->holds_count,
            'status' => $this->status->value,
            'status_label' => \translate('tariff.status', $this->status),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'archived_at' => $this->archived_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
