<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Common\BaseFormatter;


/**
 * @mixin \App\Models\User
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
            'username' => $this->username,
            'person_id' => $this->person_id,
            'person' => $this->whenLoaded('person', function () {
                return new \App\Components\Person\Formatter($this->person);
            }),
            'is_instructor' => $this->whenLoaded('instructor', function () {
                return null !== $this->instructor;
            }),
            'is_student' => $this->whenLoaded('student', function () {
                return null !== $this->student;
            }),
            'is_customer' => $this->whenLoaded('customer', function () {
                return null !== $this->customer;
            }),
            'roles' => $this->getRoleNames(),
            'roles_labels' => collect($this->getRoleNames())->map(fn ($role) => \translate('user.role', $role)),
            'permissions' => $this->getPermissionNames(),
            'active_shift' => $this->whenLoaded(
                'active_shift',
                fn () => new \App\Components\Shift\Formatter($this->active_shift),
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'approved_at' => $this->approved_at?->toDateTimeString(),
            'seen_at' => $this->seen_at?->toDateTimeString(),
            'status' => $this->status,
            'status_label' => \translate('user.status', $this->status),
        ];
    }
}
