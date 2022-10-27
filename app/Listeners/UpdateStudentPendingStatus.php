<?php

namespace App\Listeners;

use App\Events\Visit\VisitCreatedEvent;
use App\Events\Visit\VisitDeletedEvent;
use App\Events\Visit\VisitEvent;

class UpdateStudentPendingStatus extends StudentListener
{
    public function handle(VisitEvent $visitEvent): void
    {
        match (true) {
            $visitEvent instanceof VisitCreatedEvent =>  $this->students->activatePotentialStudent($visitEvent->visit->student, $visitEvent->user),
            $visitEvent instanceof VisitDeletedEvent => $this->students->dectivateStudent($visitEvent->visit->student, $visitEvent->user),
        };
    }
}