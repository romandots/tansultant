<?php
/**
 * File: PersonalAccessClientSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

/**
 * Class PersonalAccessClientSeeder
 */
class PersonalAccessClientSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        /** @var ClientRepository $clientsRepository */
        $clientsRepository = \app(ClientRepository::class);
        $clientsRepository->createPersonalAccessClient(
            null, \config('app.name'), \config('app.url')
        );
    }
}
