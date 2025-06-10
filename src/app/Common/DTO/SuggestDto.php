<?php

namespace App\Common\DTO;

class SuggestDto extends DtoWithUser
{
    public ?string $query;
    public array $with = [];
    public array $with_count = [];
    public int $limit = 10;
}