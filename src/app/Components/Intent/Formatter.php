<?php

declare(strict_types=1);

namespace App\Components\Intent;

use App\Common\BaseFormatter;
use App\Models\Enum\IntentEventType;

/**
 * @mixin \App\Models\Intent
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
                return $this->event_type === IntentEventType::LESSON
                    ? new \App\Components\Lesson\Formatter($this->event) : null;
            }),
            'event_type' => $this->event_type,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
