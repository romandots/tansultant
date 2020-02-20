<?php
/**
 * File: CourseResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CourseResource
 * @package App\Http\Resources
 * @mixin \App\Models\Course
 */
class CourseResource extends JsonResource
{
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
            'age_restrictions' => $this->age_restrictions,
            'age_restrictions_string' => $this->getAgeRestrictionsString($this->age_restrictions),
            'instructor' => $this->whenLoaded('instructor', function () {
                return new InstructorResource($this->instructor);
            }),
            'starts_at' => $this->starts_at ? $this->starts_at->toDateString() : null,
            'ends_at' => $this->ends_at ? $this->ends_at->toDateString() : null,
        ];
    }


    public function getAgeRestrictionsString(array $ageRestrictions): string
    {
        $from = $ageRestrictions['from'] ?? null;
        $to = $ageRestrictions['to'] ?? null;

        if ($from && $to) {
            return \trans('course.age_restrictions.from_to', $from, $to);
        }

        if ($from) {
            return \trans('course.age_restrictions.from', $from);
        }

        if ($to) {
            return \trans('course.age_restrictions.to', $to);
        }

        return \trans('course.age_restrictions.any');
    }
}
