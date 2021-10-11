<?php
/**
 * File: LessonResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources\ManagerApi;

use App\Http\Resources\CourseResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\UserResource;
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
                return new InstructorResource($this->instructor);
            }),
            'course' => $this->whenLoaded('course', function () {
                return new CourseResource($this->course);
            }),
            'controller' => $this->whenLoaded('controller', function () {
                return new UserResource($this->controller);
            }),
            'is_closed' => (bool)$this->closed_at,
            'is_canceled' => (bool)$this->canceled_at,
            'starts_at' => $this->starts_at?->toDateTimeString(),
            'ends_at' => $this->ends_at?->toDateTimeString(),
            'closed_at' => $this->closed_at?->toDateTimeString(),
            'canceled_at' => $this->canceled_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
