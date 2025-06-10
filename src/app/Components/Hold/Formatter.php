<?php

declare(strict_types=1);

namespace App\Components\Hold;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Hold
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
            'starts_at' => $this->starts_at?->toDateTimeString(),
            'ends_at' => $this->ends_at?->toDateTimeString(),
        ];
    }
}
