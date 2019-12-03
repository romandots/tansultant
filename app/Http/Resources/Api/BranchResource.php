<?php
/**
 * File: BranchResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-08-1
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Branch;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BranchResource
 * @package App\Http\Resources\Api
 * @mixin \App\Models\Branch
 */
class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
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
