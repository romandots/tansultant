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
            'student' => $this->whenLoaded('student', static function () {
                return new StudentResource($this->student);
            }),
            'manager' => $this->whenLoaded('manager', static function () {
                return new UserResource($this->manager);
            }),
            'lesson' => $this->whenLoaded('event', static function () {
                return $this->event_type === Lesson::class
                    ? new LessonResource($this->event) : null;
            }),
            'event_type' => \base_classname($this->event_type),
            'payment_type' => \base_classname($this->payment_type),
//            'payment' => $this->whenLoaded('payment', static function () {
//                return new PaymentResource($this->payment);
//            }),
//            'is_paid' => $this->whenLoaded('payment', static function () {
//                return $this->payment->status === Payment::STATUS_PAID;
//            }),
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
