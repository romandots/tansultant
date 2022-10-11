<?php

declare(strict_types=1);

namespace App\Components\Credit;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public int $amount;
    public string $customer_id;
    public string|null $transaction_id;
}