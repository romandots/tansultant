<?php
/**
 * File: CustomerRole.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

class CustomerRole extends Permission
{
    public const ROLE = 'customer';
    public const PERMISSIONS = [];
}
