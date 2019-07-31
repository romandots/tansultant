<?php
/**
 * File: Classroom.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api\DTO;

/**
 * Class Classroom
 * @package App\Http\Requests\Api\DTO
 */
class Classroom
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $branch_id;

    /**
     * @var string|null
     */
    public $color;

    /**
     * @var int|null
     */
    public $capacity;

    /**
     * @var int|null
     */
    public $number;
}
