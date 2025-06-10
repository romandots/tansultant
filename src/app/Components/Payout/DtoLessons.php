<?php

declare(strict_types=1);

namespace App\Components\Payout;

/**
 * @param string[] $lessons_ids
 */
class DtoLessons extends \App\Common\DTO\DtoWithUser
{
    public string $payout_id;
    public ?string $formula_id;
    public array $lessons_ids;
}