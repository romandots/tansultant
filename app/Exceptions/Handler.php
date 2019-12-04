<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Auth\UnauthorizedException;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends BaseExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     * @var array
     */
    protected $dontReport = [
        UnauthorizedException::class,
    ];

    /**
     * Returns a list of user-defined exception handlers
     * Feel free to put your handlers here, and also you can override internal handlers here
     * @return array
     */
    protected function getCustomHandlers(): array
    {
        return [
            UnauthorizedException::class => [$this, 'unauthorizedException'],
        ];
    }

    /**
     * @param UnauthorizedException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorizedException(UnauthorizedException $exception): \Illuminate\Http\JsonResponse {
        $output = ['message' => \trans('exceptions.' . $exception->getMessage())];
        $payload = $exception->getData();
        if (null !== $payload) {
            $output['data'] = $payload;
        }

        return \json_response($output, $exception->getStatusCode());
    }
}
