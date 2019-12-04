<?php
/**
 * File: UserTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Services\Permissions\UsersPermissions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class UserTest
 */
class UserTest extends \Tests\TestCase
{
    use WithFaker, CreatesFakePerson, CreatesFakeUser;

    protected const URL = 'manager_api/v1/users';

    protected const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'person',
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
            UsersPermissions::CREATE,
            UsersPermissions::READ,
            UsersPermissions::UPDATE,
            UsersPermissions::DELETE
        ]);
    }

    public function testMe(): void
    {
        $url = 'auth/user';

        $this
            ->actingAs($this->me, 'api')
            ->get($url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'id' => $this->me->id,
                    'name' => $this->me->name,
                    'person' => [
                        'id' => $this->me->person->id,
                        'last_name' => $this->me->person->last_name,
                        'first_name' => $this->me->person->first_name,
                        'patronymic_name' => $this->me->person->patronymic_name,
                        'birth_date' => $this->me->person->birth_date->toDateString(),
                        'gender' => $this->me->person->gender,
                        'phone' => $this->me->person->phone,
                        'email' => $this->me->person->email,
                        'picture' => $this->me->person->picture,
                        'picture_thumb' => $this->me->person->picture_thumb,
                        'instagram_username' => $this->me->person->instagram_username,
                        'telegram_username' => $this->me->person->telegram_username,
                        'vk_uid' => $this->me->person->vk_uid,
                        'vk_url' => $this->me->person->vk_url,
                        'facebook_uid' => $this->me->person->facebook_uid,
                        'facebook_url' => $this->me->person->facebook_url,
                        'note' => $this->me->person->note,
                        'created_at' => $this->me->person->created_at->toDateTimeString()
                    ],
                    'seen_at' => $this->me->seen_at->toDateTimeString(),
                    'created_at' => $this->me->created_at->toDateTimeString()
                ]
            ]);
    }

    public function testShowDenied(): void
    {
        $me = $this->createFakeUser();
        $user = $this->createFakeUser();

        $url = self::URL . '/' . $user->id;

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
        $user = $this->createFakeUser();
        $person = $user->person;

        $url = self::URL . '/' . $user->id;

        $this
            ->actingAs($this->me, 'api')
            ->get($url)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
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
                    'seen_at' => $user->seen_at->toDateTimeString(),
                    'created_at' => $user->created_at->toDateTimeString()
                ]
            ]);
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

    public function testStore(): void
    {
        $data = [
            'username' => $this->faker->firstName,
            'password' => 'password',
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
                    'username' => $data['username'],
                    'name' => "{$data['last_name']} {$data['first_name']}",
                    'person' =>
                        [
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
                    'password' => 'password',
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-06-15',
                    'gender' => 'male',
                    'phone' => '+7-999-999-99-99',
                    'email' => 'some@email.com',
                    'instagram_username' => 'instaperez',
                    'telegram_username' => 'teleperez',
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ],
            ],
            [
                [
                    'username' => 'username',
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-06-15',
                    'gender' => 'male',
                    'phone' => '+7-999-999-99-99',
                    'email' => 'some@email.com',
                    'instagram_username' => 'instaperez',
                    'telegram_username' => 'teleperez',
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ],
            ],
            [
                [
                    'username' => 'username',
                    'password' => 'password',
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-06-15',
                    'gender' => 'male',
                    'phone' => '+7-999-999-99-99',
                    'email' => 'some@email.com',
                    'instagram_username' => 'instaperez',
                    'telegram_username' => 'teleperez',
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ],
            ],
            [
                [
                    'username' => 'username',
                    'password' => 'password',
                    'last_name' => 'Иванов',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-06-15',
                    'gender' => 'male',
                    'phone' => '+7-999-999-99-99',
                    'email' => 'some@email.com',
                    'instagram_username' => 'instaperez',
                    'telegram_username' => 'teleperez',
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ],
            ],
            [
                [
                    'username' => 'username',
                    'password' => 'password',
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'birth_date' => '1986-06-15',
                    'gender' => 'male',
                    'phone' => '+7-999-999-99-99',
                    'email' => 'some@email.com',
                    'instagram_username' => 'instaperez',
                    'telegram_username' => 'teleperez',
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ],
            ],
            [
                [
                    'username' => 'username',
                    'password' => 'password',
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'gender' => 'male',
                    'phone' => '+7-999-999-99-99',
                    'email' => 'some@email.com',
                    'instagram_username' => 'instaperez',
                    'telegram_username' => 'teleperez',
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ],
            ],
            [
                [
                    'username' => 'username',
                    'password' => 'password',
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-06-15',
                    'gender' => 'male',
                    'email' => 'some@email.com',
                    'instagram_username' => 'instaperez',
                    'telegram_username' => 'teleperez',
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ],
            ],
        ];
    }

    public function testCreateFromPersonDenied(): void
    {
        $me = $this->createFakeUser();

        $url = self::URL . '/from_person';

        $this
            ->post($url, [])
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->post($url, [])
            ->assertStatus(403);
    }

    public function testCreateFromPerson(): void
    {
        $person = $this->createFakePerson();
        $data = [
            'person_id' => $person->id,
            'username' => $this->faker->firstName,
            'password' => '123456'
        ];
        $url = self::URL . '/from_person';

        $this
            ->actingAs($this->me, 'api')
            ->post($url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'username' => $data['username'],
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

    public function testUpdatePasswordDenied(): void
    {
        $url = 'auth/user/password';

        $this
            ->patch($url, [
                'old_password' => '123456',
                'new_password' => '654321'
            ])
            ->assertStatus(401);
    }

    public function testUpdatePassword(): void
    {
        $oldPassword = '123456';
        $newPassword = '654321';

        $user = $this->createFakeManagerUser(['password' => \Hash::make($oldPassword)]);

        $data = [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
        ];

        $url = 'auth/user/password';

        $this
            ->actingAs($user, 'api')
            ->patch($url, $data)
            ->assertOk();

        $user->refresh();

        $this->assertTrue(\Hash::check($newPassword, $user->password));
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidPasswords
     */
    public function testInvalidUpdatePassword(array $data): void
    {
        $user = $this->createFakeManagerUser(['password' => \Hash::make('123456')]);

        $url = 'auth/user/password';

        $this
            ->actingAs($user, 'api')
            ->patch($url, $data)
            ->assertStatus(422);

        $user->refresh();

        $this->assertFalse(\Hash::check('654321', $user->password));
    }

    /**
     * @return array
     */
    public function provideInvalidPasswords(): array
    {
        return [
            [
                [
                    'old_password' => '654321',
                    'new_password' => '654321',
                ]
            ],
            [
                [
                    'old_password' => '123456',
                ]
            ]
        ];
    }

    public function testUpdateDenied(): void
    {
        $me = $this->createFakeUser();
        $user = $this->createFakeUser();

        $url = self::URL . '/' . $user->id;

        $this
            ->put($url, [])
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->put($url, [])
            ->assertStatus(403);
    }

    public function testUpdate(): void
    {
        $user = $this->createFakeUser();

        $data = [
            'username' => $this->faker->firstName,
            'name' => $this->faker->name,
            'password' => $this->faker->password
        ];

        $url = self::URL . '/' . $user->id;
        $person = $user->person;

        $this
            ->actingAs($this->me, 'api')
            ->put($url, $data)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'username' => $data['username'],
                    'name' => $data['name'],
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
                ]
            ]);

        $user->refresh();

        $this->assertTrue(\Hash::check($data['password'], $user->password));
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidUpdateData
     */
    public function testInvalidUpdate(array $data): void
    {
        if (\App\Models\User::whereUsername('existingUser')->count() === 0) {
            $this->createFakeUser(['username' => 'existingUser']);
        }
        $user = $this->createFakeUser();
        $url = self::URL . '/' . $user->id;

        $this
            ->actingAs($this->me, 'api')
            ->put($url, $data)
            ->assertStatus(422);
    }

    /**
     * @return array
     */
    public function provideInvalidUpdateData(): array
    {
        return [
            [
                [
                    'username' => 'existingUser',
                    'name' => 'Another Name',
                ]
            ],
            [
                [
                    'username' => 'a',
                    'name' => 'Another Name',
                ]
            ],
            [
                [
                    'username' => 'username',
                    'name' => 'a',
                ]
            ],
            [
                [
                    'password' => '12345',
                ]
            ],
        ];
    }

    public function testDestroyDenied(): void
    {
        $me = $this->createFakeUser();

        $user = $this->createFakeUser();

        $url = self::URL . '/' . $user->id;

        $this
            ->delete($url, [])
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->delete($url, [])
            ->assertStatus(403);
    }

    public function testDestroy(): void
    {
        $user = $this->createFakeUser();
        $person = $user->person;

        $url = self::URL . '/' . $user->id;

        $this
            ->actingAs($this->me, 'api')
            ->delete($url)
            ->assertOk();

        $this->assertDatabaseHas(\App\Models\Person::TABLE, ['id' => $person->id]);
        $this->assertDatabaseMissing(\App\Models\User::TABLE, ['id' => $user->id]);
    }
}
