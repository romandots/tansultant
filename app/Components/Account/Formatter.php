<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Common\BaseFormatter;
use App\Models\Enum\AccountOwnerType;

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
            'owner_type' => $this->owner_type->value,
            'owner' => $this->whenLoaded('owner', function () {
                return match ($this->owner_type) {
                    AccountOwnerType::STUDENT => new \App\Components\Student\Formatter($this->owner),
                    AccountOwnerType::INSTRUCTOR => new \App\Components\Instructor\Formatter($this->owner),
                    AccountOwnerType::BRANCH => new \App\Components\Branch\Formatter($this->owner),
                };
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
