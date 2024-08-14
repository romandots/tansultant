<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Requests\Webhooks\TelegramRequest;
use App\Services\CustomerService\CustomerServiceAction;
use App\Services\CustomerService\CustomerServiceException;
use App\Services\CustomerService\CustomerService;

class TelegramSelfServiceController extends \App\Http\Controllers\Controller
{
    public function __invoke(CustomerService $customerService, TelegramRequest $request): void
    {
        $person = $request->getPerson();
        try {
            match ($request->getAction()) {
                CustomerServiceAction::GET_BALANCE->value => $customerService->sendBalanceNotification($person),
                CustomerServiceAction::GET_SUBSCRIPTIONS->value => $customerService->sendSubscriptionsNotification($person),
                default => throw new CustomerServiceException('Invalid action'),
            };
        } catch (\Exception $exception) {
            abort(400, $exception->getMessage());
        }
    }
}