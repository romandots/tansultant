<?php
/**
 * File: IntentResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Intent;
use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IntentResource
 * @package App\Http\Resources
 * @mixin Intent
 */
class IntentResource extends JsonResource
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
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
