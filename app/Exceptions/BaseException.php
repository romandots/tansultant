<?php
/**
 * File: BaseExceptions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

/**
 * Class BaseException
 * @package App\Exceptions
 *
 * Base exception class which is JsonRenderable and can output result as JsonResponse
 */
abstract class BaseException extends \RuntimeException implements JsonRenderable
{
    /**
     * Data to render in response
     *
     * @var mixed
     */
    private $data;

    /**
     * BaseException constructor.
     * @param string $message
     * @param null $data
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', $data = null, int $code = 0, \Throwable $previous = null)
    {
        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 500;
    }

    /**
     * @return JsonResponse
     */
    public function renderAsJson(): JsonResponse
    {
        return \response()->json([
            'message' => $this->getMessage(),
            'data' => $this->getData()
        ], $this->getStatusCode());
    }
}
