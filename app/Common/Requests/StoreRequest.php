<?php

namespace App\Common\Requests;

use App\Common\DTO\DtoWithUser;

abstract class StoreRequest extends BaseRequest
{
    public function buildDto(DtoWithUser $dto)
    {
        $dto->user = $this->user();
        return $dto;
    }
}