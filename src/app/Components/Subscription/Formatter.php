<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseFormatter;
use App\Components\Loader;

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
        $component = Loader::subscriptions();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'holds' => $this
                ->whenLoaded('holds', fn () => \App\Components\Hold\Formatter::collection($this->holds)),
            'active_hold_id' => $this->hold_id,
            'active_hold' => $this
                ->whenLoaded('active_hold', fn () => new \App\Components\Hold\Formatter($this->active_hold)),
            'student_id' => $this->student_id,
            'student' => $this
                ->whenLoaded('student', fn () => new \App\Components\Student\Formatter($this->student)),
            'tariff_id' => $this->tariff_id,
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
            'days_count' => (int)$this->days_count,
            'courses_count' => (int)$this->courses_count,
            'visits_count' => (int)$this->visits_count,
            'holds_count' => (int)$this->holds_count,
            'days_left' => $this->days_limit !== null ? (int)$this->days_left : null,
            'courses_left' => $this->courses_limit !== null ? (int)$this->courses_left : null,
            'visits_left' => $this->visits_limit !== null ? (int)$this->visits_left : null,
            'holds_left' => $this->holds_limit !== null ? (int)$this->holds_left : null,
            'status' => $this->status->value,
            'status_label' => \translate('subscription.status', $this->status->value),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'activated_at' => $this->activated_at?->toDateTimeString(),
            'expired_at' => $this->expired_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            'can_delete' => $component->canBeDeleted($this->resource),
            'can_cancel' => $component->canBeCanceled($this->resource),
            'can_prolong' => $component->canBeProlonged($this->resource),
            'can_update' => $component->canBeUpdated($this->resource),
            'can_hold' => $component->canBePaused($this->resource),
            'can_unhold' => $component->canBeUnpaused($this->resource),
        ];
    }
}
