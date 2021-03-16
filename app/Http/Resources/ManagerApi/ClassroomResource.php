<?php
/**
 * File: ClassroomResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources\ManagerApi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ClassroomResource
 * @package App\Http\Resources\Api
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
            'branch_id' => $this->branch_id,
            'branch' => $this->branch ? new BranchResource($this->branch) : null,
            'color' => $this->color,
            'capacity' => $this->capacity,
            'number' => $this->number,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
