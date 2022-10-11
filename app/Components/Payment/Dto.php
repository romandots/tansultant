<?php

declare(strict_types=1);

namespace App\Components\Payment;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public int $amount;
    public string $credit_id;
    public ?string $bonus_id;
}