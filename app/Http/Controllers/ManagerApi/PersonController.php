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
use App\Http\Requests\ManagerApi\StorePersonRequest;
use App\Http\Requests\ManagerApi\StorePersonRequest as UpdatePersonRequest;
use App\Http\Resources\PersonResource;
use App\Repository\PersonRepository;

/**
 * @todo test picture upload
 */
class PersonController extends Controller
{
    private PersonRepository $personRepository;

    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
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
