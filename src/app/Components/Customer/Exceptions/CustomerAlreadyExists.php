<?php

namespace App\Components\Customer\Exceptions;

use App\Components\Customer\WithPersonFormatter;
use App\Components\Loader;
use App\Exceptions\AlreadyExistsException;

class CustomerAlreadyExists extends AlreadyExistsException
{
    protected \App\Models\Customer $customer;

    public function __construct(\App\Models\Customer $customer)
    {
        $formattedCustomer = Loader::customers()->format($customer->load('person'), WithPersonFormatter::class);
        parent::__construct($formattedCustomer);
        $this->customer = $customer;
    }

    /**
     * @return \App\Models\Customer
     */
    public function getCustomer(): \App\Models\Customer
    {
        return $this->customer;
    }
}