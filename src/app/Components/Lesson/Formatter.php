<?php

declare(strict_types=1);

namespace App\Components\Lesson;

use App\Common\BaseFormatter;
use App\Components\Loader;

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
            'status_label' => \translate('lesson.status', $this->status),
            'type' => $this->type,
            'type_label' => \translate('lesson.type', $this->type),
            'instructor_id' => $this->instructor_id,
            'instructor' => $this->whenLoaded('instructor', function () {
                return new \App\Components\Instructor\Formatter($this->instructor);
            }),
            'course_id' => $this->course_id,
            'course' => $this->whenLoaded('course', function () {
                return new \App\Components\Course\Formatter($this->course);
            }),
            'classroom_id' => $this->classroom_id,
            'classroom' => $this->whenLoaded('classroom', function () {
                return new \App\Components\Classroom\Formatter($this->classroom);
            }),
            'controller' => $this->whenLoaded('controller', function () {
                return new \App\Components\User\Formatter($this->controller);
            }),
            'visits_count' => $this->visits_count,
            'visits_limit' => $this->visits_limit,
            'visits' => $this->whenLoaded('visits', function () {
                return \App\Components\Visit\Formatter::collection($this->visits);
            }),
            'is_closed' => (bool)$this->closed_at,
            'is_canceled' => (bool)$this->canceled_at,
            'price_id' => $this->price_id,
            'price' => $this->whenLoaded(
                'price',
                function () {
                    return new \App\Components\Price\Formatter($this->price);
                }
            ),
            'starts_at' => $this->starts_at?->toDateTimeString(),
            'ends_at' => $this->ends_at?->toDateTimeString(),
            'closed_at' => $this->closed_at?->toDateTimeString(),
            'canceled_at' => $this->canceled_at?->toDateTimeString(),
            'checked_out_at' => $this->checked_out_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'pivot' => isset($this->pivot) ?
                [
                    'formula_id' => $this->pivot?->formula_id,
                    'amount' => $this->pivot?->amount,
                    'equation' => $this->pivot?->equation,
                    'equation_description' => Loader::formulas()->describeFormulaEquation($this->pivot?->equation),
                ]
                : [],
        ];
    }
}
