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
//        $pattern = '/^(\d{1,2}):(\d{1,2}):\d{1,2}$/';
//        $replacement = '\1:\2';
        return [
            'id' => $this->id,
            'weekday' => $this->weekday,
            'starts_at' => $this->starts_at ? Carbon::parse($this->starts_at)->format('H:i:00') : null,
            'ends_at' => $this->ends_at ? Carbon::parse($this->ends_at)->format('H:i:00') : null,
            'branch_id' => $this->branch_id,
            'classroom_id' => $this->classroom_id,
            'course' => $this->whenLoaded('course', function () {
                return new CourseResource($this->course);
            }),
        ];
    }
}
