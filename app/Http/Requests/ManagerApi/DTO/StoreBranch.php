<?php
/**
 * File: Branch.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class Branch
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreBranch
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string|null
     */
    public $summary;

    /**
     * @var string|null
     */
    public $description;

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
    public $url;

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
    public $telegram_username;

    /**
     * @var string|null
     */
    public $instagram_username;

    /**
     * @var string|null
     */
    public $address;

    /**
     * @var string|null
     */
    public $number;
}
