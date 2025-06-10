<?php

declare(strict_types=1);

namespace App\Components\Classroom;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Classroom
 */
class Formatter extends BaseFormatter
{
    /**
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'branch_id' => $this->branch_id,
            'branch' => $this->branch ? new \App\Components\Branch\Formatter($this->branch) : null,
            'color' => $this->color,
            'capacity' => $this->capacity,
            'number' => $this->number,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
