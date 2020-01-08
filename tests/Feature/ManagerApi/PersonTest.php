<?php
/**
 * File: PersonTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Services\Permissions\PersonsPermissions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class PersonTest
 */
class PersonTest extends \Tests\TestCase
{
    use WithFaker, CreatesFakeUser, CreatesFakePerson;

    protected const URL = 'admin/people';
    protected const JSON_STRUCTURE = [
        'data' => [
            'id',
            'last_name',
            'first_name',
            'patronymic_name',
            'birth_date',
            'gender',
            'phone',
            'email',
            'picture',
            'picture_thumb',
            'instagram_username',
            'telegram_username',
            'vk_uid',
            'vk_url',
            'facebook_uid',
            'facebook_url',
            'note',
            'created_at'
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
            PersonsPermissions::CREATE,
            PersonsPermissions::READ,
            PersonsPermissions::UPDATE,
            PersonsPermissions::DELETE
        ]);
    }

    public function testShowDenied(): void
    {
        $me = $this->createFakeUser();
        $person = $this->createFakePerson();

        $url = self::URL . '/' . $person->id;

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
        $person = $this->createFakePerson();

        $url = self::URL . '/' . $person->id;

        $this
            ->actingAs($this->me, 'api')
            ->get($url)
            ->assertOk()
            ->assertJson([
                'data' => [
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
            ]);
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidData
     */
    public function testInvalidStore(array $data): void
    {
        $this
            ->actingAs($this->me, 'api')
            ->post(self::URL, $data)
            ->assertStatus(422);
    }

    public function testUpdateDenied(): void
    {
        $me = $this->createFakeUser();
        $person = $this->createFakePerson();

        $url = self::URL . '/' . $person->id;

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

        $person = $this->createFakePerson();

        $url = self::URL . '/' . $person->id;

        $this
            ->actingAs($this->me, 'api')
            ->put($url, $data)
            ->assertStatus(200)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'id' => $person->id,
                    'last_name' => $data['last_name'],
                    'first_name' => $data['first_name'],
                    'patronymic_name' => $data['patronymic_name'],
                    'birth_date' => $data['birth_date'],
                    'gender' => 'male',
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'picture' => $person->picture,
                    'picture_thumb' => $person->picture_thumb,
                    'instagram_username' => $data['instagram_username'],
                    'telegram_username' => $data['telegram_username'],
                    'vk_uid' => $person->vk_uid,
                    'vk_url' => 'https://vk.com/durov',
                    'facebook_uid' => $person->facebook_uid,
                    'facebook_url' => 'https://facebook.com/mark',
                    'note' => 'Some testy note',
                ]
            ]);
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidData
     */
    public function testInvalidUpdate(array $data): void
    {
        $person = $this->createFakePerson();

        $url = self::URL . '/' . $person->id;

        $this
            ->actingAs($this->me, 'api')
            ->put($url, $data)
            ->assertStatus(422);
    }

    /**
     * @return array
     */
    public function provideInvalidData(): array
    {
        return [
            [
                [
                    'last_name' => 'Иванов',
                    'patronymic_name' => 'Иванович',
                    'birth_date' => '1986-06-15',
                ],
            ],
            [
                [
                    'first_name' => 'Иван',
                    'vk_url' => 'some text',
                ],
            ],
            [
                [
                    'first_name' => 'Иван',
                    'facebook_url' => 'no url',
                ],
            ],
            [
                [
                    'first_name' => 'Иван',
                    'email' => 'not an email',
                ]
            ],
        ];
    }

    public function testDestroyDenied(): void
    {
        $me = $this->createFakeUser();
        $person = $this->createFakePerson();

        $url = self::URL . '/' . $person->id;

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
        $person = $this->createFakePerson();

        $url = self::URL . '/' . $person->id;

        $this
            ->actingAs($this->me, 'api')
            ->delete($url)
            ->assertOk();

        $this->assertDatabaseMissing(\App\Models\Person::TABLE, [
            'id' => $person->id,
            'deleted_at' => null
        ]);
    }
}
