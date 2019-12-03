<?php
/**
 * File: Intent.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class Intent
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreIntent
{
    /**
     * @var string
     */
    public $student_id;

    /**
     * @var string
     */
    public $event_id;

    /**
     * @var string
     */
    public $event_type;
}
