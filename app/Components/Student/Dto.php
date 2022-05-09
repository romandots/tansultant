<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Models\Enum\StudentStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $person_id;
    public ?string $customer_id;
    public string $name;
    public StudentStatus $status;
    public ?string $card_number = null;
}