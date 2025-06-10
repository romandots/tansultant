<?php
/**
 * File: BranchesTableSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace Database\Seeders;

use App\Components\Loader;
use Illuminate\Database\Seeder;

/**
 * Class BranchesTableSeeder
 */
class BranchesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        try {
            $branch = \App\Models\Branch::query()->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $branchDto = new \App\Components\Branch\Dto();
            $branchDto->name = 'Студия';

            $branch = Loader::branches()->create($branchDto);
        }

        try {
            \App\Models\Classroom::query()->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $classroom = new \App\Components\Classroom\Dto();
            $classroom->name = 'Зал А';
            $classroom->branch_id = $branch->id;

            Loader::classrooms()->create($classroom);
        }
    }
}
