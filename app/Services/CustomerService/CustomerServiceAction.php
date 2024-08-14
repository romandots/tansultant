<?php

namespace App\Services\CustomerService;

enum CustomerServiceAction: string
{
    case GET_BALANCE = 'get_balance';
    case GET_SUBSCRIPTIONS = 'get_subscriptions';
}