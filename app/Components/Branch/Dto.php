<?php

declare(strict_types=1);

namespace App\Components\Branch;

class Dto extends \App\Common\DTO\DtoWIthUser
{
    public ?string $id;
    public string $name;
    public ?string $summary = null;
    public ?string $description = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $url = null;
    public ?string $vk_url = null;
    public ?string $facebook_url = null;
    public ?string $telegram_username = null;
    public ?string $instagram_username = null;
    public ?AddressDto $address = null;
    public ?int $number = null;
}

