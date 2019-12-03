<?php
/**
 * File: CustomerRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Customer;
use App\Models\Person;

/**
 * Class CustomerRepository
 * @package App\Repository
 */
class CustomerRepository
{
    /**
     * @param string $id
     * @return Customer|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): ?Customer
    {
        return Customer::query()->findOrFail($id);
    }

    /**
     * @param Person $person
     * @return Customer
     * @throws \Exception
     */
    public function create(Person $person): Customer
    {
        $customer = new Customer;
        $customer->id = \uuid();
        $customer->person_id = $person->id;
        $customer->name = "{$person->last_name} {$person->first_name}";
        $customer->save();

        return $customer;
    }

    /**
     * @param Customer $customer
     * @throws \Exception
     */
    public function delete(Customer $customer): void
    {
        $customer->delete();
    }
}
