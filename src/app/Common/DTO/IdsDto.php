<?php

namespace App\Common\DTO;

class IdsDto extends DtoWithUser
{
    public string $id;
    public array $relations_ids = [];
    public array $with = [];
    public array $with_count = [];
}