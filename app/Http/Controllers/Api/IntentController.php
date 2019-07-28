<?php
/**
 * File: IntentController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLessonIntentRequest;
use App\Http\Resources\IntentResource;
use App\Repository\IntentRepository;

/**
 * Class IntentController
 * @package App\Http\Controllers
 */
class IntentController extends Controller
{
    /**
     * @var IntentRepository
     */
    private $repository;

    /**
     * IntentController constructor.
     * @param IntentRepository $repository
     */
    public function __construct(IntentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param StoreLessonIntentRequest $request
     * @return IntentResource
     */
    public function store(StoreLessonIntentRequest $request): IntentResource
    {
        $intent = $this->repository->createFromDto($request->getDto(), $request->user());

        return new IntentResource($intent);
    }

    /**
     * @param int $id
     * @return IntentResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id): IntentResource
    {
        $intent = $this->repository->find($id);

        return new IntentResource($intent);
    }

    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(int $id): void
    {
        $intent = $this->repository->find($id);
        $this->repository->delete($intent);
    }
}
