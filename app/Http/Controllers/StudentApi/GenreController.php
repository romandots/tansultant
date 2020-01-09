<?php
/**
 * File: TagsController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Controllers\StudentApi;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Repository\GenreRepository;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    private GenreRepository $repository;

    public function __construct(GenreRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all genres as array of strings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $genres = $this->repository->getAll();

        return \json_response(['data' => $genres]);
    }

    /**
     * Get only genres user is subscribed to
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubscriptions(Request $request): \Illuminate\Http\JsonResponse
    {
        $genres = $this->repository->getAllForPerson($request->user()->person);

        return \json_response(['data' => $genres]);
    }

    /**
     * @param Request $request
     * @param string $genre
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function subscribe(Request $request, string $genre): void
    {
        /** @var Person $person */
        $person = $request->user()->person;
        $tag = $this->repository->find($genre);
        $person->attachTag($tag);
    }

    /**
     * @param Request $request
     * @param string $genre
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function unsubscribe(Request $request, string $genre): void
    {
        /** @var Person $person */
        $person = $request->user()->person;
        $tag = $this->repository->find($genre);
        $person->detachTag($tag);
    }
}
