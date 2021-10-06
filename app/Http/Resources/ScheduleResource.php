<?php
/**
 * File: ScheduleResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\ManagerApi\BranchResource;
use App\Http\Resources\PublicApi\ClassroomResource;
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
            'cycle' => $this->cycle,
            'weekday' => (int)$this->weekday,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'duration' => Carbon::parse($this->ends_at)->diffInMinutes(Carbon::parse($this->starts_at)),
//            'branch' => $this->whenLoaded(
//                'branch',
//                function () {
//                    return new BranchResource($this->branch);
//                }
//            ),
            'branch_id' => $this->branch_id,
            'classroom_id' => $this->classroom_id,
//            'classroom' => $this->whenLoaded(
//                'classroom',
//                function () {
//                    return new ClassroomResource($this->classroom);
//                }
//            ),
            'course_id' => $this->course_id,
//            'course' => $this->whenLoaded(
//                'course',
//                function () {
//                    return new CourseResource($this->course);
//                }
//            ),
        ];
    }
}
