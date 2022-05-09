<?php

declare(strict_types=1);

namespace App\Components\VerificationCode;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $phone_number;
    public string $verification_code;
    public \Carbon\Carbon $expired_at;
}