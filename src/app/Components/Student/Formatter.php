<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Student
 */
class Formatter extends BaseFormatter
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'person' => $this->whenLoaded('person', function () {
                return new \App\Components\Person\Formatter($this->person);
            }),
            'person_id' => $this->person_id,
            'customer' => $this->whenLoaded('customer', function () {
                return new \App\Components\Customer\Formatter($this->customer);
            }),
            'customer_id' => $this->customer_id,
            'card_number' => $this->card_number,
            'visits_count' => $this->visits_count,
            'subscriptions_count' => $this->subscriptions_count,
            //'subscriptions' => $this->whenLoaded(
            //    'subscriptions',
            //    fn () => \App\Components\Subscription\Formatter::collection($this->subscriptions)
            //),
            'subscriptions' => $this->whenLoaded(
                'subscriptions',
                fn () => \App\Components\Subscription\Formatter::collection($this->active_subscriptions)
            ),
            'status' => $this->status,
            'status_label' => \translate('student.status', $this->status),
            'seen_at' => $this->seen_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        );
    }
}
