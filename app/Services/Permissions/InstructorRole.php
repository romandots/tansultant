<?php
/**
 * File: InstructorRole.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class InstructorRole
 * @package App\Services\Permissions
 */
class InstructorRole extends Permissions
{
    public const ROLE = 'instructor';
    public const PERMISSIONS = [];
}
