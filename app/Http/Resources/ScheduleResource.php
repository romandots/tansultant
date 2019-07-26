<?php
/**
 * File: ScheduleResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ScheduleResource
 * @package App\Http\Resources
 * @mixin \App\Models\Schedule
 */
class ScheduleResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $pattern = '/^(\d{1,2}):(\d{1,2}):\d{1,2}$/';
        $replacement = '\1:\2';
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'classroom_id' => $this->classroom_id,
            'course' => $this->whenLoaded('course', function () {
                return new CourseResource($this->course);
            }),
            'starts_at' => $this->starts_at ? $this->starts_at->toDateString() : null,
            'ends_at' => $this->ends_at ? $this->ends_at->toDateString() : null,
            'duration' => $this->duration,
            'monday' => $this->monday instanceof Carbon
                ? $this->monday->format('H:i') : \preg_replace($pattern, $replacement, $this->monday),
            'tuesday' => $this->tuesday instanceof Carbon
                ? $this->tuesday->format('H:i') : \preg_replace($pattern, $replacement, $this->tuesday),
            'wednesday' => $this->wednesday instanceof Carbon
                ? $this->wednesday->format('H:i') : \preg_replace($pattern, $replacement, $this->wednesday),
            'thursday' => $this->thursday instanceof Carbon
                ? $this->thursday->format('H:i') : \preg_replace($pattern, $replacement, $this->thursday),
            'friday' => $this->friday instanceof Carbon
                ? $this->friday->format('H:i') : \preg_replace($pattern, $replacement, $this->friday),
            'saturday' => $this->saturday instanceof Carbon
                ? $this->saturday->format('H:i') : \preg_replace($pattern, $replacement, $this->saturday),
            'sunday' => $this->sunday instanceof Carbon
                ? $this->sunday->format('H:i') : \preg_replace($pattern, $replacement, $this->sunday),
        ];
    }
}
