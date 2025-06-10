<?php

declare(strict_types=1);

namespace App\Components\Branch;

use App\Common\BaseFormatter;
use App\Models\Branch;

/**
 * @mixin \App\Models\Branch
 */
class Formatter extends BaseFormatter
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $address = \array_combine(Branch::ADDRESS_JSON,
            \array_map(function ($field) {
                return $this->address[$field] ?? null;
            }, Branch::ADDRESS_JSON)
        );

        return [
            'id' => $this->id,
            'name' => $this->name,
            'summary' => $this->summary,
            'description' => $this->description,
            'phone' => $this->phone,
            'email' => $this->email,
            'url' => $this->url,
            'vk_url' => $this->vk_url,
            'facebook_url' => $this->facebook_url,
            'telegram_username' => $this->telegram_username,
            'instagram_username' => $this->instagram_username,
            'address' => $address,
            'number' => $this->number
        ];
    }
}
