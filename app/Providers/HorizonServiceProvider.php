<?php

namespace App\Providers;

use App\Services\Permissions\UserRoles;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    protected function authorization()
    {
        Horizon::auth(function ($request) {
            return \auth('web')->user()->hasRole(UserRoles::ADMIN) || app()->environment('local');
        });
    }
}
