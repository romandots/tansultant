<?php

namespace App\Http\Controllers\ManagerApi;

use App\Components\Loader;
use App\Components\LogRecord\Formatter;
use App\Http\Controllers\Controller;
use App\Models\Enum\LogRecordObjectType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HistoryController extends Controller
{
    public function index(string $type, string $id): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $objectType = LogRecordObjectType::tryFrom($type);

        if (null === $objectType) {
            throw new NotFoundHttpException();
        }

        $records = Loader::logRecords()->getHistory($objectType, $id);

        return Formatter::collection($records);
    }
}