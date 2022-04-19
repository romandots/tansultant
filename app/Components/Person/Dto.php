<?php

declare(strict_types=1);

namespace App\Components\Person;

use App\Models\Enum\Gender;

class Dto extends \App\Common\DTO\DtoWIthUser
{
    public ?string $id;

    public ?string $last_name = null;

    public ?string $first_name = null;

    public ?string $patronymic_name = null;

    public ?\Carbon\Carbon $birth_date;

    public ?Gender $gender = null;

    public ?string $phone = null;

    public ?string $email = null;

    public ?string $instagram_username = null;

    public ?string $telegram_username = null;

    public ?string $vk_url = null;

    public ?string $facebook_url = null;

    public ?string $note = null;

    public ?\Illuminate\Http\UploadedFile $picture = null;
}