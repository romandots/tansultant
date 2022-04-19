<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Payment
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
