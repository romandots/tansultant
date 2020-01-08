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
class StoreUser
{
    /**
     * @var string
     */
    public $person_id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;
}
