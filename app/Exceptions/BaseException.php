<?php
/**
 * File: BaseExceptions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Class BaseException
 * @package App\Exceptions
 * Base exception class which is JsonRenderable and can output result as JsonResponse
 */
abstract class BaseException extends \RuntimeException implements ReadableExceptionInterface
{
    /**
     * Data to render in response
     * @var array|null $data
     */
    protected ?array $data;

    protected int $statusCode;

    /**
     * BaseException constructor.
     * @param string|null $message
     * @param array|null $data
     * @param int $statusCode
     * @param \Throwable|null $previous
     */
    public function __construct(
        ?string $message = '',
        ?array $data = null,
        int $statusCode = 500,
        \Throwable $previous =
        null
    ) {
        $this->data = $data;
        $this->statusCode = $statusCode;

        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data ?? null;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
