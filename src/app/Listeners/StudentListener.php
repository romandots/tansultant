<?php

namespace App\Listeners;

use App\Components\Loader;

class StudentListener
{
    protected \App\Components\Subscription\Facade $subscriptions;

    public function __construct()
    {
        $this->students = Loader::students();
    }
}
