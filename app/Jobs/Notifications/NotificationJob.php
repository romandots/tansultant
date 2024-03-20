<?php

namespace App\Jobs\Notifications;

use App\Common\Traits\WithLogger;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class NotificationJob implements ShouldQueue, ShouldBeUnique, Isolatable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WithLogger;

    public int $uniqueFor = 60;

    public function __construct(
        public readonly Person $person,
        public readonly string $message,
    ) { }

    public function uniqueId(): string
    {
        return md5($this->person->id . $this->message);
    }

    public static function telegram(Person $person, string $message): void
    {
        NotifyByTelegramJob::dispatch($person, $message);
    }

    public static function sms(Person $person, string $message): void
    {
        NotifyBySmsJob::dispatch($person, $message);
    }
}