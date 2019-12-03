<?php
/**
 * File: User.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class User
 * @package App\Http\Requests\ManagerApi\DTO
 */
class User
{
    /**
     * @var string $person_id
     */
    public $person_id;

    /**
     * @var string $username
     */
    public $username;

    /**
     * @var string $password
     */
    public $password;
}
