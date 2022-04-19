<?php

declare(strict_types=1);

namespace App\Components\VerificationCode;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\VerificationCode
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
            'verification_code' => $this->verification_code,
            'phone_number' => $this->phone_number,
        ];
    }
}
