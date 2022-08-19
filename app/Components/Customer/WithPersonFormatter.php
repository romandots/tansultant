<?php

declare(strict_types=1);

namespace App\Components\Customer;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Customer
 */
class WithPersonFormatter extends BaseFormatter
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
            'person' => new \App\Components\Person\Formatter($this->person)
        ];
    }
}
