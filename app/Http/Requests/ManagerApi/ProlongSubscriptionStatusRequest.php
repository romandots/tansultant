<?php
/**
 * File: StorePersonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Subscription\ProlongDto;

class ProlongSubscriptionStatusRequest extends StoreRequest
{
    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $dto = new ProlongDto($this->user());
        $dto->id = $this->getId();
        $dto->bonus_id = $this->getBonusId();

        return $dto;
    }

    private function getId(): string
    {
        return $this->route()->parameter('id');
    }

    private function getBonusId(): ?string
    {
        return $this->route()?->parameter('bonus_id');
    }

}
