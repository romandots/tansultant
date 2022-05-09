<?php

declare(strict_types=1);

namespace App\Components\Customer;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public string $person_id;
}