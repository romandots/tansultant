<?php
/**
 * File: IntentController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\StoreLessonIntentRequest;
use App\Http\Resources\IntentResource;
use App\Repository\IntentRepository;

class IntentController extends Controller
{
    private IntentRepository $repository;

    public function __construct(IntentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param StoreLessonIntentRequest $request
     * @return IntentResource
     * @throws \Exception
     */
    public function store(StoreLessonIntentRequest $request): IntentResource
    {
        $intent = $this->repository->createFromDto($request->getDto(), $request->user());
        $intent->load('student', 'manager', 'event');

        return new IntentResource($intent);
    }

    /**
     * @param string $id
     * @return IntentResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): IntentResource
    {
        $intent = $this->repository->find($id);
        $intent->load('student', 'manager', 'event');

        return new IntentResource($intent);
    }

    /**
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $intent = $this->repository->find($id);
        $this->repository->delete($intent);
    }
}
