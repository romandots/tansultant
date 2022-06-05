<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes([
    'middleware' => 'manager_api',
]);

Broadcast::channel('schedule.{date}.{branch_id}', \App\Broadcasting\ScheduleChannel::class);
Broadcast::channel('events.{type}.{id}', \App\Broadcasting\EventsChannel::class);
Broadcast::channel('user.{id}', \App\Broadcasting\UserChannel::class);
