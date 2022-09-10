<?php

declare(strict_types=1);

namespace App\Components\Schedule;

use App\Common\BaseFormatter;
use Carbon\Carbon;

/**
 * @mixin \App\Models\Schedule
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
            'name' => (string)$this->resource,
            'cycle' => $this->cycle,
            'weekday' => $this->weekday?->value,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'duration' => Carbon::parse($this->ends_at)->diffInMinutes(Carbon::parse($this->starts_at)),
            'branch' => $this->whenLoaded(
                'branch',
                function () {
                    return new \App\Components\Branch\Formatter($this->branch);
                }
            ),
            'branch_id' => $this->branch_id,
            'classroom_id' => $this->classroom_id,
            'classroom' => $this->whenLoaded(
                'classroom',
                function () {
                    return new \App\Components\Classroom\Formatter($this->classroom);
                }
            ),
            'course_id' => $this->course_id,
            'course' => $this->whenLoaded(
                'course',
                function () {
                    return new \App\Components\Course\Formatter($this->course);
                }
            ),
        ];
    }
}
