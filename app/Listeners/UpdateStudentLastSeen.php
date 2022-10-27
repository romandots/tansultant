<?php

namespace App\Listeners;

use App\Events\Visit\VisitEvent;

class UpdateStudentLastSeen extends StudentListener
{
    public function handle(VisitEvent $visitEvent): void
    {
        $this->students->updateLastSeen($visitEvent->visit->student);
    }
}