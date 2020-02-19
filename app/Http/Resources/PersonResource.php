<?php
/**
 * File: PersonResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PersonResource
 * @package App\Http\Resources
 * @mixin \App\Models\Person
 */
class PersonResource extends JsonResource
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
            'name' => \sprintf('%s %s %s', $this->last_name, $this->first_name, $this->patronymic_name),
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'patronymic_name' => $this->patronymic_name,
            'birth_date' => $this->birth_date ?  $this->birth_date->toDateString() : null,
            'age' => $this->birth_date ?  $this->birth_date->age : null,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'picture' => $this->picture,
            'picture_thumb' => $this->picture_thumb,
            'instagram_username' => $this->instagram_username,
            'telegram_username' => $this->telegram_username,
            'vk_uid' => $this->vk_uid,
            'facebook_uid' => $this->facebook_uid,
            'note' => $this->note,
            'is_customer' => (bool)$this->whenLoaded('customer', function () {
                return null !== $this->customer;
            }, false),
            'is_student' => (bool)$this->whenLoaded('student', function () {
                return null !== $this->student;
            }, false),
            'is_instructor' => (bool)$this->whenLoaded('instructor', function () {
                return null !== $this->instructor;
            }, false),
            'is_user' => (bool)$this->whenLoaded('user', function () {
                return null !== $this->user;
            }, false),
            'instructor_id' => $this->whenLoaded('instructor', function () {
                return null !== $this->instructor ?  $this->instructor->id : null;
            }),
            'student_id' => $this->whenLoaded('student', function () {
                return null !== $this->student ?  $this->student->id : null;
            }),
            'customer_id' => $this->whenLoaded('customer', function () {
                return null !== $this->customer ?  $this->customer->id : null;
            }),
            'user_id' => $this->whenLoaded('user', function () {
                return null !== $this->user ?  $this->user->id : null;
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
