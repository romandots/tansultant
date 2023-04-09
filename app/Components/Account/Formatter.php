<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Account
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
            'type' => $this->type,
            'type_label' => \translate('account.type', $this->type),
            'branch_id' => $this->branch_id,
            'branch' => $this->whenLoaded('branch', function () {
                return new \App\Components\Branch\Formatter($this->branch);
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
