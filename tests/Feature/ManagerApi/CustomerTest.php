<?php
/**
 * File: CustomerTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Services\Permissions\CustomersPermissions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\CreatesFakeCustomer;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class PersonTest
 */
class CustomerTest extends \Tests\TestCase
{
    use WithFaker, CreatesFakeUser, CreatesFakePerson, CreatesFakeCustomer;

    protected const URL = 'admin/customers';

    protected const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'person',
            'contract',
            'seen_at',
            'created_at',
        ]
    ];

    /**
     * @var \App\Models\User
     */
    private $me;

    public function setUp(): void
    {
        parent::setUp();
        $this->me = $this->createFakeManagerUser([], [
            CustomersPermissions::CREATE,
            CustomersPermissions::READ,
            CustomersPermissions::UPDATE,
            CustomersPermissions::DELETE
        ]);
    }

    public function testShowDenied(): void
    {
        $me = $this->createFakeUser();
        $customer = $this->createFakeCustomer();

        $url = self::URL . '/' . $customer->id;

        $this
            ->get($url)
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->get($url)
            ->assertStatus(403);
    }

    public function testStoreDenied(): void
    {
        $me = $this->createFakeUser();

        $this
            ->post(self::URL, [])
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->post(self::URL, [])
            ->assertStatus(403);
    }

    public function testDestroyDenied(): void
    {
        $me = $this->createFakeUser();
        $customer = $this->createFakeCustomer();

        $url = self::URL . '/' . $customer->id;

        $this
            ->delete($url, [])
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->delete($url, [])
            ->assertStatus(403);
    }

    public function testShow(): void
    {
        $customer = $this->createFakeCustomer();
        $person = $customer->person;

        $url = self::URL . '/' . $customer->id;

        $this
            ->actingAs($this->me, 'api')
            ->get($url)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
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
                    'seen_at' => $customer->seen_at->toDateTimeString(),
                    'created_at' => $customer->created_at->toDateTimeString()
                ]
            ]);
    }

    public function testStore(): void
    {
        $data = [
            'last_name' => $this->faker->lastName,
            'first_name' => $this->faker->firstName,
            'patronymic_name' => $this->faker->firstName,
            'birth_date' => $this->faker->date(),
            'gender' => 'male',
            'phone' => $this->faker->numerify('+7-###-###-##-##'),
            'email' => $this->faker->email,
            'instagram_username' => $this->faker->word,
            'telegram_username' => $this->faker->word,
            'vk_url' => 'https://vk.com/durov',
            'facebook_url' => 'https://facebook.com/mark',
            'note' => 'Some testy note',
        ];

        $this
            ->actingAs($this->me, 'api')
            ->post(self::URL, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'name' => "{$data['last_name']} {$data['first_name']}",
                    'person' => [
                        'last_name' => $data['last_name'],
                        'first_name' => $data['first_name'],
                        'patronymic_name' => $data['patronymic_name'],
                        'birth_date' => $data['birth_date'],
                        'gender' => 'male',
                        'phone' => $data['phone'],
                        'email' => $data['email'],
                        'picture' => null,
                        'picture_thumb' => null,
                        'instagram_username' => $data['instagram_username'],
                        'telegram_username' => $data['telegram_username'],
                        'vk_uid' => null,
                        'vk_url' => 'https://vk.com/durov',
                        'facebook_uid' => null,
                        'facebook_url' => 'https://facebook.com/mark',
                        'note' => 'Some testy note',
                    ],
                ]
            ]);
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidStoreData
     */
    public function testInvalidStore(array $data): void
    {
        $this
            ->actingAs($this->me, 'api')
            ->post(self::URL, $data)
            ->assertStatus(422);
    }

    /**
     * @return array
     */
    public function provideInvalidStoreData(): array
    {
        return [
            [
                [
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'phone' => '8-999-999-99-99'
                ],
            ],
            [
                [
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-01-08',
                    'phone' => '8-999-999-99-99'
                ],
            ],
            [
                [
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'birth_date' => '1986-01-08',
                    'phone' => '8-999-999-99-99'
                ],
            ],
            [
                [
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-01-08'
                ],
            ],
        ];
    }

    public function testCreateFromPerson(): void
    {
        $person = $this->createFakePerson();
        $data = [
            'person_id' => $person->id,
        ];
        $url = self::URL . '/from_person';

        $this
            ->actingAs($this->me, 'api')
            ->post($url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'person' => [
                        'id' => $person->id,
                        'last_name' => $person->last_name,
                        'first_name' => $person->first_name,
                        'patronymic_name' => $person->patronymic_name,
                        'birth_date' => $person->birth_date ? $person->birth_date->toDateString() : null,
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
                    ]
                ]
            ]);
    }

    public function testDestroy(): void
    {
        $customer = $this->createFakeCustomer();
        $person = $customer->person;

        $url = self::URL . '/' . $customer->id;

        $this
            ->actingAs($this->me, 'api')
            ->delete($url)
            ->assertOk();

        $this->assertDatabaseHas(\App\Models\Person::TABLE, ['id' => $person->id]);
        $this->assertDatabaseMissing(\App\Models\Customer::TABLE, [
            'id' => $customer->id,
            'deleted_at' => null
        ]);
    }
}
