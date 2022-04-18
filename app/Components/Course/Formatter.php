<?php

declare(strict_types=1);

namespace App\Components\Course;

use App\Common\BaseFormatter;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\ScheduleResource;
use App\Models\Genre;

/**
 * @mixin \App\Models\Course
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
                return new \App\Components\Instructor\Formatter($this->instructor);
            }),
            'genres' => $this->tagsWithType(Genre::class)->pluck('name')->all(),
            'schedules' => $this->whenLoaded('instructor', function () {
                \App\Components\Schedule\Formatter::collection($this->schedules);
            }),
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
