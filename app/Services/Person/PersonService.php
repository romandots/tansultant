<?php
/*
 * File: PersonService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

namespace App\Services\Person;


use App\Http\Requests\DTO\Contracts\PaginatedInterface;
use App\Repository\PersonRepository;

class PersonService
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getMeta(PaginatedInterface $searchPeople): array
    {
        $totalRecords = $this->repository->countFiltered($searchPeople->filter);
        return $searchPeople->getMeta($totalRecords);
    }
}