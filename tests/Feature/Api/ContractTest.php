<?php
/**
 * File: ContractTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Models\Contract;
use App\Services\Permissions\CustomersPermissions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\CreatesFakeContract;
use Tests\Traits\CreatesFakeCustomer;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class ContractTest
 */
class ContractTest extends \Tests\TestCase
{
    use WithFaker, CreatesFakeUser, CreatesFakePerson, CreatesFakeCustomer, CreatesFakeContract;

    protected const URL = '/api/customers/%d/contract';
    protected const JSON_STRUCTURE = [
        'data' => [
            'serial',
            'number',
            'branch_id',
            'status',
            'is_signed',
            'is_terminated',
            'is_pending',
            'signed_at',
            'terminated_at',
            'created_at',
            'customer' => [
                'person',
                'permissions'
            ]
        ]
    ];

    /**
     * @var \App\Models\User
     */
    private $me;

    public function setUp(): void
    {
        parent::setUp();
        $this->me = $this->createFakeUser([], [
            CustomersPermissions::CREATE_CUSTOMERS,
            CustomersPermissions::READ_CUSTOMERS,
            CustomersPermissions::DELETE_CUSTOMERS,
            CustomersPermissions::SIGN_CONTRACTS,
            CustomersPermissions::TERMINATE_CONTRACTS,
        ]);
    }

    public function testShowDenied(): void
    {
        $me = $this->createFakeUser();
        $contract = $this->createFakeContract();
        $customer = $contract->customer;

        $url = \sprintf(self::URL, $customer->id);

        $this
            ->get($url)
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->get($url)
            ->assertStatus(403);
    }

    public function testShow(): void
    {
        $contract = $this->createFakeContract([
            'status' => Contract::STATUS_PENDING,
            'signed_at' => null,
            'terminated_at' => null
        ]);

        $this->assertDatabaseHas(Contract::TABLE, [
            'id' => $contract->id,
            'status' => Contract::STATUS_PENDING,
            'signed_at' => null,
            'terminated_at' => null
        ]);

        $customer = $contract->customer;
        $person = $customer->person;
        $url = \sprintf(self::URL, $customer->id);

        $this
            ->actingAs($this->me, 'api')
            ->get($url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'customer' => [
                        'person' => [
                            'id' => $person->id,
                            'last_name' => $person->last_name,
                            'first_name' => $person->first_name,
                            'patronymic_name' => $person->patronymic_name,
                            'birth_date' => $person->birth_date->toDateString(),
                            'gender' => $person->gender,
                            'phone' => $person->phone,
                            'email' => $person->email,
                            'picture' => $person->picture,
                            'picture_thumb' => $person->picture_thumb,
                            'instagram_username' => $person->instagram_username,
                            'telegram_username' => $person->telegram_username,
                            'vk_uid' => $person->vk_uid,
                            'vk_url' => $person->vk_url,
                            'facebook_uid' => $person->facebook_uid,
                            'facebook_url' => $person->facebook_url,
                            'note' => $person->note,
                            'created_at' => $person->created_at->toDateTimeString()
                        ],
                    ],
                    'status' => 'pending',
                    'is_signed' => false,
                    'is_terminated' => false,
                    'is_pending' => true,
                    'created_at' => $contract->created_at->toDateTimeString(),
                    'signed_at' => $contract->signed_at ? $contract->signed_at->toDateTimeString() : null,
                    'terminated_at' => $contract->terminated_at
                        ? $contract->terminated_at->toDateTimeString() : null,
                ]
            ]);
    }

    public function testSignDenied(): void
    {
        $me = $this->createFakeUser();
        $contract = $this->createFakeContract();
        $customer = $contract->customer;

        $url = \sprintf(self::URL, $customer->id);

        $this
            ->post($url)
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->post($url)
            ->assertStatus(403);
    }

    public function testSign(): void
    {
        $contract = $this->createFakeContract([
            'status' => Contract::STATUS_PENDING,
            'signed_at' => null,
            'terminated_at' => null
        ]);
        $customer = $contract->customer;

        $url = \sprintf(self::URL, $customer->id);

        $this
            ->actingAs($this->me, 'api')
            ->post($url)
            ->assertOk();

        $this->assertDatabaseHas(Contract::TABLE, [
            'id' => $contract->id
        ]);
        $this->assertDatabaseMissing(\App\Models\Contract::TABLE, [
            'id' => $contract->id,
            'signed_at' => null
        ]);
    }

    public function testTerminateDenied(): void
    {
        $me = $this->createFakeUser();
        $contract = $this->createFakeContract();
        $customer = $contract->customer;

        $url = \sprintf(self::URL, $customer->id);

        $this
            ->delete($url)
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->delete($url)
            ->assertStatus(403);
    }

    public function testTerminate(): void
    {
        $contract = $this->createFakeContract([
            'status' => \App\Models\Contract::STATUS_PENDING,
            'signed_at' => null,
            'terminated_at' => null
        ]);
        $customer = $contract->customer;

        $url = \sprintf(self::URL, $customer->id);

        $this
            ->actingAs($this->me, 'api')
            ->delete($url)
            ->assertOk();

        $this->assertDatabaseHas(\App\Models\Contract::TABLE, [
            'id' => $contract->id
        ]);
        $this->assertDatabaseMissing(\App\Models\Contract::TABLE, [
            'id' => $contract->id,
            'terminated_at' => null
        ]);
    }
}
