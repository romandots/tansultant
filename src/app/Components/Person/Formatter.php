<?php

declare(strict_types=1);

namespace App\Components\Person;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Person
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
            'name' => \sprintf('%s %s %s', $this->last_name, $this->first_name, $this->patronymic_name),
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'patronymic_name' => $this->patronymic_name,
            'birth_date' => $this->birth_date?->toDateString(),
            'age' => $this->birth_date?->age,
            'gender' => $this->gender,
            'phone' => $this->phone ? phone_format($this->phone) : null,
            'email' => $this->email,
            'picture' => $this->picture,
            'picture_thumb' => $this->picture_thumb,
            'instagram_username' => $this->instagram_username,
            'telegram_username' => $this->telegram_username,
            'vk_uid' => $this->vk_uid,
            'facebook_uid' => $this->facebook_uid,
            'note' => $this->note,
            'is_customer' => (bool)$this->whenLoaded('customers', function () {
                return null !== $this->customer;
            }, null),
            'is_student' => (bool)$this->whenLoaded('students', function () {
                return null !== $this->student;
            }, null),
            'is_instructor' => (bool)$this->whenLoaded('instructors', function () {
                return null !== $this->instructor;
            }, null),
            'is_user' => (bool)$this->whenLoaded('users', function () {
                return null !== $this->user;
            }, null),
            'instructor_id' => $this->whenLoaded('instructors', function () {
                return $this->instructor?->id;
            }),
            'student_id' => $this->whenLoaded('students', function () {
                return $this->student?->id;
            }),
            'customer_id' => $this->whenLoaded('customers', function () {
                return $this->customer?->id;
            }),
            'user_id' => $this->whenLoaded('users', function () {
                return $this->user?->id;
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
