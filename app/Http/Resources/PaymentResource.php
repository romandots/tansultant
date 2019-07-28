<?php
/**
 * File: PaymentResource.inc
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Lesson;
use App\Models\Visit;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PaymentResource
 * @package App\Http\Resources
 * @mixin \App\Models\Payment
 */
class PaymentResource extends JsonResource
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
            'amount' => $this->amount,
            'type' => $this->type,
            'transfer_type' => $this->transfer_type,
            'transfer_type_label' => \trans('payment.transfer_type.' . $this->transfer_type),
            'status' => $this->status,
            'status_label' => \trans('payment.status.' . $this->status),
            'object_type' => $this->object_type,
            'object' => $this->whenLoaded('object', function () {
                switch ($this->object_type) {
                    case Lesson::class:
                        return new LessonResource($this->object);
                    case Visit::class:
                        return new VisitResource($this->object);
                    default:
                        return null;
                }
            }),
            'account' => $this->whenLoaded('account', function () {
                return new AccountResource($this->account);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'external_id' => $this->external_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'confirmed_at' => $this->confirmed_at ? $this->confirmed_at->toDateTimeString() : null,
            'canceled_at' => $this->canceled_at ? $this->canceled_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : null,
        ];
    }
}
