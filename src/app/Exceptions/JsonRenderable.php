<?php
/**
 * File: JsonRenderable.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

/**
 * Interface JsonRenderable
 * @package Brainex\ApiComponents\Exceptions
 *
 * Adding ability to render exception as json
 */
interface JsonRenderable
{
    /**
     * Render exception as JsonResponse
     *
     * @return JsonResponse
     */
    public function renderAsJson(): JsonResponse;
}
