<?php
/**
 * File: VisitResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Lesson;
use App\Models\Payment;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class VisitResource
 * @package App\Http\Resources
 * @mixin \App\Models\Visit
 */
class VisitResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student' => $this->whenLoaded('student', function () {
                return new StudentResource($this->student);
            }),
            'manager' => $this->whenLoaded('manager', function () {
                return new UserResource($this->manager);
            }),
            'lesson' => $this->whenLoaded('event', function () {
                return $this->event_type === Lesson::class
                    ? new LessonResource($this->event) : null;
            }),
            'event_type' => \base_classname($this->event_type),
            'payment_type' => \base_classname($this->payment_type),
            'payment' => $this->whenLoaded('payment', function () {
                return Payment::class === $this->payment_type
                    ? new PaymentResource($this->payment) : null;
            }),
//            'promocode' => $this->whenLoaded('payment', static function () {
//                return Promocode::class === $this->payment_type
//                    ? new PromocodeResource($this->payment) : null;
//            }),
            'is_paid' => $this->whenLoaded('payment', function () {
                return null !== $this->payment && $this->payment->status === Payment::STATUS_CONFIRMED;
            }),
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
