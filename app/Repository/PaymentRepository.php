<?php
/**
 * File: PaymentRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Payment;

/**
 * Class PaymentRepository
 * @package App\Repository
 */
class PaymentRepository
{
    public function create(\App\Services\Payment\DTO\Payment $dto): Payment
    {
        return new Payment;
    }
}
