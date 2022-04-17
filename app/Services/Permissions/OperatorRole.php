<?php
/**
 * File: OperatorRole.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class OperatorRole
 * @package App\Services\Permissions
 */
class OperatorRole extends Permissions
{
    public const ROLE = 'operator';
    public const PERMISSIONS = [];
}
