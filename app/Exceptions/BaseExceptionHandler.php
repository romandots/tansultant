<?php
/**
 * File: BaseExceptionHandler.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieAuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class BaseExceptionHandler
 * @package App\Exceptions
 */
class BaseExceptionHandler extends ExceptionHandler
{
    /**
     * A list of custom exception handlers
     * This is an array of callbacks that called for custom exceptions
     * The key must be a name of exception class to render
     * Closure MUST return a response object
     * Passed arguments are instances of (1st) exception and (2nd) request
     * @var \Closure[]
     */
    protected $computedHandlers;

    /**
     * BaseHandler constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->computedHandlers = \array_merge(
            $this->getInternalHandlers(),
            $this->getCustomHandlers()
        );

        parent::__construct($container);
    }

    /**
     * Returns a list of internal exceptions handlers
     * @return array
     */
    protected function getInternalHandlers(): array
    {
        return [
            JsonRenderable::class => function (JsonRenderable $exception) {
                return $exception->renderAsJson();
            },
            ModelNotFoundException::class => [$this, 'modelNotFoundJson'],
            AuthenticationException::class => [$this, 'unauthenticatedJson'],
            AuthorizationException::class => [$this, 'unauthorizedJson'],
            SpatieAuthorizationException::class => [$this, 'spatieUnauthorizedJson'],
            ValidationException::class => [$this, 'validationJson'],
            HttpException::class => [$this, 'httpExceptionJson']
        ];
    }

    /**
     * Returns a list of user-defined exception handlers
     * Feel free to put your handlers here, and also you can override internal handlers here
     * @return array
     */
    protected function getCustomHandlers(): array
    {
        return [];
    }

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, \Exception $exception)
    {
        $needJson = $request->expectsJson() || $this->isApiRequest($request);

        if ($needJson) {
            foreach ($this->computedHandlers as $key => $handler) {
                if ($exception instanceof $key) {
                    return $handler($exception, $request);
                }
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Get JsonResponse for AuthenticationException exception
     * @param AuthenticationException $e
     * @return JsonResponse
     */
    protected function unauthenticatedJson(AuthenticationException $e): JsonResponse
    {
        return \response()->json(['message' => 'unauthenticated'], 401);
    }

    /**
     * Get JsonResponse for AuthorizationException exception
     * @param AuthorizationException $e
     * @return JsonResponse
     */
    protected function unauthorizedJson(AuthorizationException $e): JsonResponse
    {
        return \response()->json(['message' => 'access_denied'], 403);
    }

    /**
     * Get JsonResponse for spatie/laravel-permission AuthorizationException exception
     * @param SpatieAuthorizationException $e
     * @return JsonResponse
     */
    protected function spatieUnauthorizedJson(SpatieAuthorizationException $e): JsonResponse
    {
        $response = ['message' => 'access_denied'];

        if (config('permission.display_permission_in_exception')) {
            $roles = $e->getRequiredRoles();
            $permissions = $e->getRequiredPermissions();

            if ($roles) {
                $response['data']['roles_needed'] = $roles;
            }

            if ($permissions) {
                $response['data']['permissions_needed'] = $permissions;
            }
        }

        return \response()->json($response, 403);
    }

    /**
     * Get JsonResponse for HttpException exception
     * @param HttpException $exception
     * @return JsonResponse
     */
    protected function httpExceptionJson(HttpException $exception): JsonResponse
    {
        $code = $exception->getStatusCode();
        $message = $exception->getMessage() ?: Response::$statusTexts[$code];

        return \response()->json(['message' => $message], $code, $exception->getHeaders());
    }

    /**
     * Get JsonResponse for ModelNotFound exception
     * @param ModelNotFoundException $exception
     * @return JsonResponse
     */
    protected function modelNotFoundJson(ModelNotFoundException $exception): JsonResponse
    {
        return \response()->json([
            'message' => 'entity_not_found',
            'data' => [
                'model' => $exception->getModel(),
                'ids' => $exception->getIds()
            ]
        ], 404);
    }

    /**
     * Determines is request to API route
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isApiRequest($request): bool
    {
        if (!$request instanceof \Illuminate\Http\Request || null === $request->route()) {
            return false;
        }

        return \in_array('api', (array)$request->route()->getAction('middleware'), true);
    }

    /**
     * Convert an authentication exception into a response.
     * This is overriding of standard laravel's method.
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? $this->unauthenticatedJson($exception)
            : redirect()->guest(route('login'));
    }

    /**
     * Create a response object from the given validation exception.
     * This is overriding of standard laravel's method.
     * @param \Illuminate\Validation\ValidationException $e
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        return $request->expectsJson()
            ? $this->validationJson($e)
            : $this->invalid($request, $e);
    }

    /**
     * Convert a validation exception into a JSON response.
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationJson(ValidationException $exception): JsonResponse
    {
        $errors = \format_validation_errors($exception->validator->failed());

        return response()->json(['message' => 'validation_error', 'data' => $errors], $exception->status);
    }
}
