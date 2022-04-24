<?php
/**
 * File: PermissionsTableSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Database\Seeders;

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
     * @throws \ReflectionException
     */
    public function run(): void
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->runSystem();
        $this->runUsers();
        $this->runPeople();
        $this->runBranchesAndClassrooms();
        $this->runCoursesAndLessons();
        $this->runVisitsAndIntents();
        $this->runFinance();
    }

    protected function _runPermissions(string $permission): void
    {
        $permissions = $permission::getAllNames();
        $descriptions = $permission::getInitialDescriptions();

        $this->createPermissions($permissions, $descriptions);
    }

    /**
     * @throws \ReflectionException
     */
    private function runSystem(): void
    {
        $this->_runPermissions(\App\Services\Permissions\SystemPermission::class);
    }

    /**
     * @throws \ReflectionException
     */
    private function runUsers(): void
    {
        $this->_runPermissions(\App\Services\Permissions\UsersPermission::class);
    }

    /**
     * @throws \ReflectionException
     */
    private function runPeople(): void
    {
        $this->_runPermissions(\App\Services\Permissions\PersonsPermission::class);
        $this->_runPermissions(\App\Services\Permissions\StudentsPermission::class);
        $this->_runPermissions(\App\Services\Permissions\CustomersPermission::class);
        $this->_runPermissions(\App\Services\Permissions\InstructorsPermission::class);
    }

    /**
     * @throws \ReflectionException
     */
    private function runBranchesAndClassrooms(): void
    {
        $this->_runPermissions(\App\Services\Permissions\BranchesPermission::class);
        $this->_runPermissions(\App\Services\Permissions\ClassroomsPermission::class);
    }

    /**
     * @throws \ReflectionException
     */
    private function runCoursesAndLessons(): void
    {
        $this->_runPermissions(\App\Services\Permissions\CoursesPermission::class);
        $this->_runPermissions(\App\Services\Permissions\SchedulesPermission::class);
        $this->_runPermissions(\App\Services\Permissions\LessonsPermission::class);
    }

    /**
     * @throws \ReflectionException
     */
    private function runVisitsAndIntents(): void
    {
        $this->_runPermissions(\App\Services\Permissions\IntentsPermission::class);
        $this->_runPermissions(\App\Services\Permissions\VisitsPermission::class);
    }

    /**
     * @throws \ReflectionException
     */
    private function runFinance(): void
    {
        $this->_runPermissions(\App\Services\Permissions\AccountsPermission::class);
        $this->_runPermissions(\App\Services\Permissions\BonusesPermission::class);
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
