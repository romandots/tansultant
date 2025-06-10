<?php

declare(strict_types=1);

namespace App\Components\Price;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Price
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
            'price' => $this->price,
            'special_price' => $this->special_price,
        ];
    }
}
