<?php
/**
 * File: StorePerson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class StorePerson
 * @package App\Http\Requests\DTO
 */
class StorePerson
{
    /**
     * @var string|null
     */
    public $last_name;

    /**
     * @var string|null
     */
    public $first_name;

    /**
     * @var string|null
     */
    public $patronymic_name;

    /**
     * @var \Carbon\Carbon
     */
    public $birth_date;

    /**
     * @var string|null
     */
    public $gender;

    /**
     * @var string|null
     */
    public $phone;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var string|null
     */
    public $instagram_username;

    /**
     * @var string|null
     */
    public $telegram_username;

    /**
     * @var string|null
     */
    public $vk_url;

    /**
     * @var string|null
     */
    public $facebook_url;

    /**
     * @var string|null
     */
    public $note;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $picture;
}
