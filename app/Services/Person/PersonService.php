<?php
/*
 * File: PersonService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

namespace App\Services\Person;


use App\Http\Requests\DTO\SearchPeople;
use App\Repository\PersonRepository;

class PersonService
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getMeta(SearchPeople $searchPeople): array
    {
        $totalRecords = $this->repository->countByFilter($searchPeople);
        return [
            'offset' => $searchPeople->offset,
            'limit' => $searchPeople->limit,
            'total' => $totalRecords
        ];
    }
}