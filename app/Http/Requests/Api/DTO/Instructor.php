<?php
/**
 * File: StoreInstructor.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api\DTO;

/**
 * Class StoreInstructor
 * @package App\Http\Requests\Api\DTO
 */
class Instructor
{
    /**
     * @var
     */
    public $description;

    /**
     * @var
     */
    public $status;

    /**
     * @var
     */
    public $display;
}
