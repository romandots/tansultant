<?php
/**
 * File: UsersTableSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        if (User::query()->where('username', 'admin')->count() === 0) {
            /** @var User $user */
            $user = User::query()
                ->firstOrCreate([
                    'id' => \uuid(),
                    'name' => 'Admin',
                    'username' => 'admin',
                    'password' => \Hash::make('12345678')
                ]);
            $user->assignRole(\App\Services\Permissions\UserRoles::ADMIN);
        }
    }
}
