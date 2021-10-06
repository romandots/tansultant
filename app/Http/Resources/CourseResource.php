<?php
/**
 * File: CourseResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Genre;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CourseResource
 * @package App\Http\Resources
 * @mixin \App\Models\Course
 */
class CourseResource extends JsonResource
{
    private mixed $age_restrictions_from;

    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'status_label' => \trans('course.status.' . $this->status),
            'summary' => $this->summary,
            'description' => $this->description,
            'display' => $this->display,
            'picture' => $this->picture,
            'picture_thumb' => $this->picture_thumb,
            'age_restrictions_from' => $this->age_restrictions['from'],
            'age_restrictions_to' => $this->age_restrictions['to'],
            'age_restrictions_string' => $this->getAgeRestrictionsString($this->age_restrictions),
            'instructor_id' => $this->instructor_id,
            'instructor' => $this->whenLoaded('instructor', function () {
                return new InstructorResource($this->instructor);
            }),
            'genres' => $this->tagsWithType(Genre::class)->pluck('name')->all(),
            'schedules' => ScheduleResource::collection($this->schedules),
            'starts_at' => $this->starts_at?->toDateString(),
            'ends_at' => $this->ends_at?->toDateString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }


    public function getAgeRestrictionsString(array $ageRestrictions): string
    {
        $from = $ageRestrictions['from'] ?? null;
        $to = $ageRestrictions['to'] ?? null;

        if ($from && $to) {
            return \trans('course.age_restrictions.from_to', ['from' => $from, 'to' => $to]);
        }

        if ($from) {
            return \trans('course.age_restrictions.from', ['from' => $from]);
        }

        if ($to) {
            return \trans('course.age_restrictions.to', ['to' => $to]);
        }

        return \trans('course.age_restrictions.any');
    }
}
