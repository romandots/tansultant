<?php

namespace App\Components\Price\Policy;

use App\Models\Enum\StudentStatus;
use App\Models\Lesson;
use App\Models\Student;

class VisitPricePolicy implements Contract\PricePolicyInterface
{
    protected Lesson $lesson;
    protected Student $student;

    public function __construct(Lesson $lesson, Student $student)
    {
        $this->lesson = $lesson;
        $this->student = $student;
    }

    public function getPrice(): float
    {
        $lessonVisitPrice = match ($this->student->status) {
            StudentStatus::ACTIVE => $this->lesson?->schedule?->price?->special_price ?? $this->lesson?->schedule?->price?->price,
            default => $this->lesson?->schedule?->price?->price,
        };

        if (null === $lessonVisitPrice) {
            return 0;
        }

        return (float)$lessonVisitPrice;

        //if (null === $this->student->personal_discount || 0 > $lessonVisitPrice || 100 < $lessonVisitPrice) {
        //    return $lessonVisitPrice;
        //}
        //
        //return (float)($lessonVisitPrice / 100 * $this->student->personal_discount);
    }
}