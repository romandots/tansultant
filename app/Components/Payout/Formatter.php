<?php

declare(strict_types=1);

namespace App\Components\Payout;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Payout
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
            'amount' => $this->amount,
            'status' => $this->status->value,
            'period_from' => $this->period_from->toDateString(),
            'period_to' => $this->period_to->toDateString(),
            'status_label' => \translate('payout.status', $this->status),
            'instructor_id' => $this->instructor_id,
            'instructor' => $this->whenLoaded('instructor', function () {
                return new \App\Components\Instructor\Formatter($this->instructor);
            }),
            'branch_id' => $this->branch_id,
            'branch' => $this->whenLoaded('branch', function () {
                return new \App\Components\Branch\Formatter($this->branch);
            }),
            'lessons' => $this->whenLoaded('lessons', function () {
                return \App\Components\Lesson\Formatter::collection($this->lessons);
            }),
            'lessons_count' => $this->lessons_count,
            'report_link' => $this->report_link,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
