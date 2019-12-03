<?php
/**
 * File: ClassroomResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources\PublicApi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ClassroomResource
 * @package App\Http\Resources\PublicApi
 * @mixin \App\Models\Classroom
 */
class ClassroomResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'branch' => $this->whenLoaded('branch', function () {
                return new BranchResource($this->branch);
            }),
            'color' => $this->color,
            'capacity' => $this->capacity,
            'number' => $this->number,
        ];
    }
}
