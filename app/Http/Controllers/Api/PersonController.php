<?php
/**
 * File: PersonController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePersonRequest;
use App\Http\Requests\Api\StorePersonRequest as UpdatePersonRequest;
use App\Http\Resources\PersonResource;
use App\Repository\PersonRepository;

/**
 * Class PersonController
 * @package App\Http\Controllers\Api
 * @todo test picture upload
 */
class PersonController extends Controller
{
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * PersonController constructor.
     * @param PersonRepository $personRepository
     */
    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    /**
     * @param StorePersonRequest $request
     * @return PersonResource
     */
    public function store(StorePersonRequest $request): PersonResource
    {
        $person = $this->personRepository->create($request->getDto());

        return new PersonResource($person);
    }

    /**
     * @param int $id
     * @return PersonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id): PersonResource
    {
        $person = $this->personRepository->find($id);

        return new PersonResource($person);
    }

    /**
     * @param int $id
     * @param UpdatePersonRequest $request
     * @return PersonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, UpdatePersonRequest $request): PersonResource
    {
        $person = $this->personRepository->find($id);
        $this->personRepository->update($person, $request->getDto());

        return new PersonResource($person);
    }

    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(int $id): void
    {
        $person = $this->personRepository->find($id);
        $this->personRepository->delete($person);
    }
}
