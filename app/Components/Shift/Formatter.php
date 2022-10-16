<?php

declare(strict_types=1);

namespace App\Components\Shift;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Shift
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
            'total_income' => $this->total_income,
            'status' => $this->status->value,
            'status_label' => translate('shift', $this->status),
            'branch_id' => $this->branch_id,
            'branch' => $this->whenLoaded(
                'branch',
                fn () => new \App\Components\Branch\Formatter($this->branch),
            ),
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded(
                'user',
                fn () => new \App\Components\User\Formatter($this->user),
            ),
            'shift' => $this->whenLoaded(
                'shift',
                fn () => new \App\Components\Shift\Formatter($this->shift),
            ),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->created_at?->toDateTimeString(),
            'closed_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
