<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Instructor
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
            'person' => $this->whenLoaded('person', function () {
                return new \App\Components\Person\Formatter($this->person);
            }),
            'description' => $this->description,
            'picture' => $this->picture,
            'display' => (bool)$this->display,
            'status' => $this->status,
            'status_label' => \trans('instructor.status.' . $this->status),
            'seen_at' => $this->seen_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
