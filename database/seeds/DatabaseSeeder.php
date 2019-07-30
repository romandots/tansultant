<?php
/**
 * File: DatabaseSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */
declare(strict_types=1);

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(BranchesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
