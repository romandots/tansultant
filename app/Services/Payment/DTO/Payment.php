<?php
/**
 * File: Payment.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Payment\DTO;

/**
 * Class Payment
 * @package App\Services\Payment\DTO
 */
class Payment
{
    /**
     * @var int
     */
    public $amount;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $object_type;

    /**
     * @var int|null
     */
    public $object_id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $transfer_type;

    /**
     * @var string
     */
    public $status;

    /**
     * @var int|null
     */
    public $account_id;

    /**
     * @var string
     */
    public $external_id;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var \Carbon\Carbon|null
     */
    public $confirmed_at;
}
