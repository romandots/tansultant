<?php

declare(strict_types=1);

namespace App\Components\Contract;

use App\Models\Enum\ContractStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public ?string $serial;
    // public ?int $number; -- this one is automatic
    public string $branch_id;
    public string $customer_id;
    public string $student_id;
    public ContractStatus $status;
}