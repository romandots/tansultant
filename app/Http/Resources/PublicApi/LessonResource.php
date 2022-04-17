<?php
/**
 * File: LessonResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources\PublicApi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LessonResource
 * @package App\Http\Resources
 * @mixin \App\Models\Lesson
 */
class LessonResource extends JsonResource
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
            'status_label' => \trans('lesson.' . $this->status),
            'type' => $this->type,
            'type_label' => \trans('lesson.' . $this->type),
            'instructor' => $this->whenLoaded('instructor', function () {
                return $this->instructor->name;
            }),
            'course' => $this->whenLoaded('course', function () {
                return $this->course->name;
            }),
            'classroom' => $this->whenLoaded('classroom', function () {
                return $this->classroom->name;
            }),
            'starts_at' => $this->starts_at?->toDateTimeString(),
            'ends_at' => $this->ends_at?->toDateTimeString(),
            'is_closed' => (bool)$this->closed_at,
            'is_canceled' => (bool)$this->canceled_at,
        ];
    }
}
