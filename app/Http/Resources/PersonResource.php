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
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'patronymic_name' => $this->patronymic_name,
            'birth_date' => $this->birth_date ?  $this->birth_date->toDateString() : null,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'picture' => $this->picture,
            'picture_thumb' => $this->picture_thumb,
            'instagram_username' => $this->instagram_username,
            'telegram_username' => $this->telegram_username,
            'vk_uid' => $this->vk_uid,
            'vk_url' => $this->vk_url,
            'facebook_uid' => $this->facebook_uid,
            'facebook_url' => $this->facebook_url,
            'note' => $this->note,
            'customer' => $this->whenLoaded('customer', function () {
                return new CustomerResource($this->customer);
            }),
            'student' => $this->whenLoaded('student', function () {
                return new StudentResource($this->student);
            }),
            'instructor' => $this->whenLoaded('instructor', function () {
                return new InstructorResource($this->instructor);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'is_customer' => $this->whenLoaded('customer', function () {
                return null !== $this->customer;
            }),
            'is_student' => $this->whenLoaded('student', function () {
                return null !== $this->student;
            }),
            'is_instructor' => $this->whenLoaded('instructor', function () {
                return null !== $this->instructor;
            }),
            'is_user' => $this->whenLoaded('user', function () {
                return null !== $this->user;
            }),
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
