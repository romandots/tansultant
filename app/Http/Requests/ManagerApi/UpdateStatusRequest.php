<?php
/**
 * File: StorePersonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\BaseRequest;

abstract class UpdateStatusRequest extends BaseRequest
{

    public function rules(): array
    {
        return  [];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $dtoClass = $this->getDtoClass();
        $dto = new $dtoClass($this->getUser(), $this->getRecordId());
        $dto->status = $this->getStatus();

        return $dto;
    }

    abstract protected function getDtoClass(): string;

    abstract protected function getStatusEnum(): string;

    protected function getStatus(): object
    {
        $statusEnum = $this->getStatusEnum();
        return $statusEnum::from($this->getStatusName());
    }
    public function getRecordId(): string
    {
        return $this->route()->parameter('id');
    }

    public function getStatusName(): string
    {
        return $this->route()->parameter('status');
    }
}
