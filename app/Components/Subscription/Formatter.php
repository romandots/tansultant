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
            'active_hold' => $this
                ->whenLoaded('active_hold', fn () => new \App\Components\Hold\Formatter($this->active_hold)),
            'student' => $this
                ->whenLoaded('student', fn () => new \App\Components\Student\Formatter($this->student)),
            'tariff' => $this
                ->whenLoaded('tariff', fn () => new \App\Components\Tariff\Formatter($this->tariff)),
            'courses' => $this
                ->whenLoaded('courses', fn () => \App\Components\Course\Formatter::collection($this->courses)),
            'payments' => $this
                ->whenLoaded('payments', fn () => \App\Components\Payment\Formatter::collection($this->payments)),
            'payments_count' => (int)$this->payments_count,
            'days_limit' => $this->days_limit,
            'courses_limit' => $this->courses_limit,
            'visits_limit' => $this->visits_limit,
            'holds_limit' => $this->holds_limit,
            'days_count' => $this->days_count,
            'courses_count' => $this->courses_count,
            'visits_count' => $this->visits_count,
            'holds_count' => $this->holds_count,
            'days_left' => null !== $this->days_limit ? $this->days_limit - $this->days_count : null,
            'courses_left' => null !== $this->courses_limit ? $this->courses_limit - $this->courses_count : null,
            'visits_left' => null !== $this->visits_limit ? $this->visits_limit - $this->visits_count : null,
            'holds_left' => null !== $this->holds_limit ? $this->holds_limit - $this->holds_count : null,
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
