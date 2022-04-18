<?php

declare(strict_types=1);

namespace App\Components\Lesson;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Lesson
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
            'status_label' => \trans('lesson.' . $this->status->value),
            'type' => $this->type,
            'type_label' => \trans('lesson.' . $this->type->value),
            'instructor' => $this->whenLoaded('instructor', function () {
                return new \App\Components\Instructor\Formatter($this->instructor);
            }),
            'course' => $this->whenLoaded('course', function () {
                return new \App\Components\Course\Formatter($this->course);
            }),
            'classroom' => $this->whenLoaded('classroom', function () {
                return new \App\Components\Classroom\Formatter($this->classroom);
            }),
            'controller' => $this->whenLoaded('controller', function () {
                return new \App\Components\User\Formatter($this->controller);
            }),
            'is_closed' => (bool)$this->closed_at,
            'is_canceled' => (bool)$this->canceled_at,
            'starts_at' => $this->starts_at?->toDateTimeString(),
            'ends_at' => $this->ends_at?->toDateTimeString(),
            'closed_at' => $this->closed_at?->toDateTimeString(),
            'canceled_at' => $this->canceled_at?->toDateTimeString(),
            'checked_out_at' => $this->checked_out_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
