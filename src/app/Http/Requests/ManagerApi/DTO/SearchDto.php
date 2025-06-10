<?php

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\DtoWithUser;

class SearchDto extends DtoWithUser
{
    public string $query;
    public int $limit = 10;
}