<?php
/**
 * File: StorePerson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\DTO;

/**
 * Class StorePerson
 * @package App\Http\Requests\DTO
 */
class StorePerson
{
    /**
     * @var string|null
     */
    public ?string $last_name = null;

    /**
     * @var string|null
     */
    public ?string $first_name = null;

    /**
     * @var string|null
     */
    public ?string $patronymic_name = null;

    /**
     * @var \Carbon\Carbon|null
     */
    public \Carbon\Carbon $birth_date;

    /**
     * @var string|null
     */
    public ?string $gender = null;

    /**
     * @var string|null
     */
    public ?string $phone = null;

    /**
     * @var string|null
     */
    public ?string $email = null;

    /**
     * @var string|null
     */
    public ?string $instagram_username = null;

    /**
     * @var string|null
     */
    public ?string $telegram_username = null;

    /**
     * @var string|null
     */
    public ?string $vk_url = null;

    /**
     * @var string|null
     */
    public ?string $facebook_url = null;

    /**
     * @var string|null
     */
    public ?string $note = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public ?\Illuminate\Http\UploadedFile $picture = null;
}
