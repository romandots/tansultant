<?php

declare(strict_types=1);

namespace App\Components\Hold;

use App\Common\BaseComponentFacade;
use App\Models\Hold;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createHoldForSubscription(Subscription $subscription, User $user): Hold
    {
        if ($subscription->load('active_hold')->active_hold !== null) {
            return $subscription->active_hold;
        }

        $holdDto = new \App\Components\Hold\Dto($user);
        $holdDto->subscription_id = $subscription->id;
        $holdDto->starts_at = Carbon::now();
        return $this->getService()->create($holdDto);
    }

    public function endOrDeleteHold(Hold $hold, \App\Models\User $user): void
    {
        if ($hold->getDuration()) {
            $this->getService()->endHold($hold, $user);
            return;
        }

        $this->getService()->delete($hold, $user);
    }
}