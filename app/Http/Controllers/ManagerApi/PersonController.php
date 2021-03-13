<?php
/**
 * File: PersonController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Services\Person\PersonService;
use App\Http\Requests\ManagerApi\SearchPeopleRequest;
use App\Http\Requests\ManagerApi\StorePersonRequest;
use App\Http\Requests\ManagerApi\StorePersonRequest as UpdatePersonRequest;
use App\Http\Resources\PersonResource;
use App\Repository\PersonRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @todo test picture upload
 */
class PersonController extends Controller
{
    private PersonRepository $personRepository;
    private PersonService $personService;

    public function __construct(PersonRepository $personRepository, PersonService $personService)
    {
        $this->personRepository = $personRepository;
        $this->personService = $personService;
    }

    /**
     * @param StorePersonRequest $request
     * @return PersonResource
     * @throws \Exception
     */
    public function store(StorePersonRequest $request): PersonResource
    {
        $person = $this->personRepository->createFromDto($request->getDto());

        return new PersonResource($person);
    }

    /**
     * @param SearchPeopleRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(SearchPeopleRequest $request): AnonymousResourceCollection
    {
        $searchPeople = $request->getDto();
        $people = $this->personRepository->findByFilter($searchPeople);
        $meta = $this->personService->getMeta($searchPeople);

        return PersonResource::collection($people)->additional(['meta' => $meta]);
    }

    /**
     * @param string $id
     * @return PersonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): PersonResource
    {
        $person = $this->personRepository->find($id);

        return new PersonResource($person);
    }

    /**
     * @param string $id
     * @param UpdatePersonRequest $request
     * @return PersonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(string $id, UpdatePersonRequest $request): PersonResource
    {
        $person = $this->personRepository->find($id);
        $this->personRepository->update($person, $request->getDto());

        return new PersonResource($person);
    }

    /**
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $person = $this->personRepository->find($id);
        $this->personRepository->delete($person);
    }
}
