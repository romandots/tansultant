<?php
/**
 * File: LessonVisit.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api\DTO;

/**
 * Class LessonVisit
 * @package App\Http\Requests\Api\DTO
 */
class Visit
{
    /**
     * @var int
     */
    public $student_id;

    /**
     * @var int
     */
    public $event_id;

    /**
     * @var string
     */
    public $event_type;

    /**
     * @var string
     */
    public $payment_type;

    /**
     * @var int|null
     */
    public $payment_id;
}
