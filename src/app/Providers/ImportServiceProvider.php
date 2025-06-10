<?php

namespace App\Providers;

use App\Services\Import\Maps\BranchesMap;
use App\Services\Import\Maps\ClassroomsMap;
use App\Services\Import\Maps\CoursesMap;
use App\Services\Import\Maps\StudentsMap;
use App\Services\Import\Maps\SubscriptionsMap;
use App\Services\Import\Maps\TariffsMap;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    protected array $mappers = [
        BranchesMap::class,
        ClassroomsMap::class,
        StudentsMap::class,
        TariffsMap::class,
        CoursesMap::class,
        SubscriptionsMap::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->mappers as $mapper) {
            $this->app->singleton($mapper);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
