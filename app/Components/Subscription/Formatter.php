<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Subscription
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
            'student' => $this
                ->whenLoaded('student', fn () => new \App\Components\Student\Formatter($this->student)),
            'tariff' => $this
                ->whenLoaded('tariff', fn () => new \App\Components\Tariff\Formatter($this->tariff)),
            'courses' => $this
                ->whenLoaded('courses', fn () => \App\Components\Course\Formatter::collection($this->courses)),
            'courses_count' => $this->courses_count,
            'visits_count' => $this->visits_count,
            'days_count' => $this->days_count,
            'holds_count' => $this->holds_count,
            'status' => $this->status->value,
            'status_label' => \translate('subscription.status', $this->status->value),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'activated_at' => $this->activated_at?->toDateTimeString(),
            'expired_at' => $this->expired_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
