<?php
/**
 * File: AcccountResource.inc
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Instructor;
use App\Models\Student;
use App\Services\Account\AccountService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AccountResource
 * @package App\Http\Resources
 * @mixin \App\Models\Account
 */
class AccountResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var AccountService $service */
        $service = \app(AccountService::class);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'type_label' => \trans('account.type.' . $this->type),
            'owner_type' => \base_classname($this->owner_type),
            'owner' => $this->whenLoaded('owner', function () {
                switch ($this->owner_type) {
                    case 'App\Models\Branch':
                        return $this->owner;
                    case Student::class:
                        return new StudentResource($this->owner);
                    case Instructor::class:
                        return new InstructorResource($this->owner);
                    default:
                        return null;
                }
            }),
            'amount' => $service->getAmount($this->resource),
            'bonus_amount' => $service->getBonusAmount($this->resource),
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
