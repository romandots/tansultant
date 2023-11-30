<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Components\Loader;
use App\Components\Transaction as Component;
use App\Http\Requests\ManagerApi\SearchTransactionsRequest;
use App\Http\Requests\ManagerApi\StoreTransactionRequest;
use App\Http\Requests\ManagerApi\UpdateTransactionRequest;
use Illuminate\Http\JsonResponse;

/**
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection index()
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection _search(\App\Common\Requests\SearchRequest $request)
 * @method array suggest(\App\Common\Requests\SuggestRequest $request)
 * @method Component\Formatter show(string $id)
 * @method Component\Formatter _store(\App\Common\Requests\StoreRequest $request)
 * @method Component\Formatter _update(string $id, \App\Common\Requests\StoreRequest $request)
 * @method void destroy(string $id, \Illuminate\Http\Request $request)
 * @method void restore(string $id, \Illuminate\Http\Request $request)
 * @method Component\Facade getFacade()
 * @method \Illuminate\Http\Resources\Json\JsonResource makeResource(\App\Models\Contract $record)
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection makeResourceCollection(\Illuminate\Support\Collection $collection)
 */
class TransactionController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: [],
            singleRecordRelations: [],
        );
    }

    public function search(SearchTransactionsRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StoreTransactionRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, UpdateTransactionRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    public function qr(string $id): JsonResponse
    {
        $transaction = $this->getFacade()->find($id);
        $qrCode = Loader::transactions()->getQrCode($transaction);

        return response()->json([
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'comment' => $transaction->name,
            'image' => $qrCode->getImage()->content,
            'image_type' => $qrCode->getImage()->mediaType,
        ]);
    }
}
