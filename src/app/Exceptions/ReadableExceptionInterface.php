<?php
/**
 * File: ReadableExceptionInterface.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Interface ReadableExceptionInterface
 * @package App\Exceptions
 */
interface ReadableExceptionInterface
{
    /**
     * @return array|null
     */
    public function getData(): ?array;

    /**
     * @return int|null
     */
    public function getStatusCode(): ?int;

    /**
     * @return int|null
     */
    public function getMessage();
}
