<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Common\BaseFormatter;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PublicApi\LessonResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Models\Enum\PaymentStatus;
use App\Models\Lesson;
use App\Models\Payment;

/**
 * @mixin \App\Models\Visit
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
            'student' => $this->whenLoaded('student', function () {
                return new \App\Components\Student\Formatter($this->student);
            }),
            'manager' => $this->whenLoaded('manager', function () {
                return new \App\Components\User\Formatter($this->manager);
            }),
            'lesson' => $this->whenLoaded('event', function () {
                return $this->event_type === Lesson::class
                    ? new \App\Components\Lesson\Formatter($this->event) : null;
            }),
            'event_type' => \base_classname($this->event_type),
            'payment_type' => \base_classname($this->payment_type),
            'payment' => $this->whenLoaded('payment', function () {
                return Payment::class === $this->payment_type
                    ? new \App\Components\Payment\Formatter($this->payment) : null;
            }),
//            'promocode' => $this->whenLoaded('payment', static function () {
//                return Promocode::class === $this->payment_type
//                    ? new \App\Components\Promocode\Formatter($this->payment) : null;
//            }),
            'is_paid' => $this->whenLoaded('payment', function () {
                return null !== $this->payment && $this->payment->status === PaymentStatus::CONFIRMED;
            }),
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
