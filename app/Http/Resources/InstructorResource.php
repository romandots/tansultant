<?php
/**
 * File: InstructorResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class InstructorResource
 * @package App\Http\Resources
 * @mixin \App\Models\Instructor
 */
class InstructorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'person' => $this->whenLoaded('person', function () {
                return new PersonResource($this->person);
            }),
            'description' => $this->description,
            'picture' => $this->picture,
            'display' => (bool)$this->display,
            'status' => $this->status,
            'status_label' => \trans($this->status),
            'permissions' => $this->getPermissionNames(),
            'seen_at' => $this->seen_at ? $this->seen_at->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
