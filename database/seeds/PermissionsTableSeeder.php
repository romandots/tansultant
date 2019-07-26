<?php
/**
 * File: PermissionsTableSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionsTableSeeder
 */
class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     * @throws ReflectionException
     */
    public function run(): void
    {
        $this->runSystem();
        $this->runUsers();
        $this->runPeople();
        $this->runCourses();
    }

    /**
     * @throws ReflectionException
     */
    private function runSystem(): void
    {
        $permissions = \App\Services\Permissions\SystemPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\SystemPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);
    }

    /**
     * @throws ReflectionException
     */
    private function runUsers(): void
    {
        $permissions = \App\Services\Permissions\UsersPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\UsersPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);
    }

    /**
     * @throws ReflectionException
     */
    private function runPeople(): void
    {
        $permissions = \App\Services\Permissions\PersonsPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\PersonsPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);

        $permissions = \App\Services\Permissions\StudentsPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\StudentsPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);

        $permissions = \App\Services\Permissions\CustomersPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\CustomersPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);

        $permissions = \App\Services\Permissions\InstructorsPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\InstructorsPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);
    }

    /**
     * @throws ReflectionException
     */
    private function runCourses(): void
    {
        $permissions = \App\Services\Permissions\CoursesPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\CoursesPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);

        $permissions = \App\Services\Permissions\SchedulesPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\SchedulesPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);

        $permissions = \App\Services\Permissions\LessonsPermissions::getAllNames();
        $descriptions = \App\Services\Permissions\LessonsPermissions::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);
    }

    /**
     * @param array $permissions
     * @param array $descriptions
     */
    private function createPermissions(array $permissions, array $descriptions): void
    {
        foreach ($permissions as $permission) {
            try {
                $perm = \Spatie\Permission\Models\Permission::findByName($permission, 'api');
                /** @var Permission $perm */
                if ($perm->description !== $descriptions[$permission]) {
                    $perm->description = $descriptions[$permission];
                }

                $perm->save();
            } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                \Spatie\Permission\Models\Permission::create([
                    'name' => $permission,
                    'description' => $descriptions[$permission] ?? null,
                    'guard_name' => 'api'
                ]);
            }
        }
    }
}
