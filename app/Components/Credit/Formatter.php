<?php

declare(strict_types=1);

namespace App\Components\Credit;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Credit
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
        ];
    }
}
