<?php
/**
 * File: InstructorTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Services\Permissions\InstructorsPermissions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class PersonTest
 */
class InstructorTest extends \Tests\TestCase
{
    use WithFaker, CreatesFakeUser, CreatesFakePerson, CreatesFakeInstructor;

    protected const URL = 'manager_api/v1/instructors';

    protected const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'person',
            'description',
            'picture',
            'display',
            'status',
            'status_label',
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
            InstructorsPermissions::CREATE,
            InstructorsPermissions::READ,
            InstructorsPermissions::UPDATE,
            InstructorsPermissions::DELETE
        ]);
    }

    public function testShowDenied(): void
    {
        $me = $this->createFakeUser();
        $instructor = $this->createFakeInstructor(['status' => \App\Models\Instructor::STATUS_HIRED]);

        $url = self::URL . '/' . $instructor->id;

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

    public function testUpdateDenied(): void
    {
        $me = $this->createFakeUser();
        $instructor = $this->createFakeInstructor(['status' => \App\Models\Instructor::STATUS_HIRED]);

        $url = self::URL . '/' . $instructor->id;

        $this
            ->put($url, [])
            ->assertStatus(401);

        $this
            ->actingAs($me, 'api')
            ->put($url, [])
            ->assertStatus(403);
    }

    public function testDestroyDenied(): void
    {
        $me = $this->createFakeUser();
        $instructor = $this->createFakeInstructor(['status' => \App\Models\Instructor::STATUS_HIRED]);

        $url = self::URL . '/' . $instructor->id;

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
        $instructor = $this->createFakeInstructor(['status' => \App\Models\Instructor::STATUS_HIRED]);
        $person = $instructor->person;

        $url = self::URL . '/' . $instructor->id;

        $this
            ->actingAs($this->me, 'api')
            ->get($url)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
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
                    'status' => $instructor->status,
                    'status_label' => \trans($instructor->status),
                    'seen_at' => $instructor->seen_at->toDateTimeString(),
                    'created_at' => $instructor->created_at->toDateTimeString()
                ]
            ]);
    }

    public function testStore(): void
    {
        $data = [
            'status' => \App\Models\Instructor::STATUS_HIRED,
            'description' => 'Some teacher info',
            'display' => true,
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
                    'status' => \App\Models\Instructor::STATUS_HIRED,
                    'status_label' => \trans(\App\Models\Instructor::STATUS_HIRED),
                    'description' => 'Some teacher info',
                    'display' => true,
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
                    'status' => 'impossible',
                    'display' => true
                ],
            ],
            [
                [
                    'description' => 'Описание',
                    'display' => true
                ],
            ],
            [
                [
                    'status' => 'hired',
                    'display' => 'true'
                ],
            ],
        ];
    }

    public function testCreateFromPerson(): void
    {
        $person = $this->createFakePerson();
        $data = [
            'status' => \App\Models\Instructor::STATUS_HIRED,
            'description' => 'Some teacher info',
            'display' => true,
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
                    'name' => "{$person->last_name} {$person->first_name}",
                    'status' => \App\Models\Instructor::STATUS_HIRED,
                    'description' => 'Some teacher info',
                    'display' => true,
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

    public function testUpdate(): void
    {
        $data = [
            'status' => \App\Models\Instructor::STATUS_FIRED,
            'description' => 'Some other info',
            'display' => false,
        ];

        $instructor = $this->createFakeInstructor([
            'status' => \App\Models\Instructor::STATUS_HIRED,
            'description' => 'Some teacher info',
            'display' => true,
        ]);
        $person = $instructor->person;

        $url = self::URL . '/' . $instructor->id;

        $this
            ->actingAs($this->me, 'api')
            ->put($url, $data)
            ->assertStatus(200)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
                    'status' => \App\Models\Instructor::STATUS_FIRED,
                    'status_label' => \trans(\App\Models\Instructor::STATUS_FIRED),
                    'description' => 'Some other info',
                    'display' => false,
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
                    'seen_at' => $instructor->seen_at->toDateTimeString(),
                    'created_at' => $instructor->created_at->toDateTimeString()
                ]
            ]);
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidUpdateData
     */
    public function testInvalidUpdate(array $data): void
    {
        $instructor = $this->createFakeInstructor();

        $url = self::URL . '/' . $instructor->id;

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
                    'status' => 'impossible',
                    'display' => true
                ],
            ],
            [
                [
                    'description' => 'Описание',
                    'display' => true
                ],
            ],
            [
                [
                    'status' => 'hired',
                    'display' => 'true'
                ],
            ],
        ];
    }

    public function testDestroy(): void
    {
        $instructor = $this->createFakeInstructor();
        $person = $instructor->person;

        $url = self::URL . '/' . $instructor->id;

        $this
            ->actingAs($this->me, 'api')
            ->delete($url)
            ->assertOk();

        $this->assertDatabaseHas(\App\Models\Person::TABLE, ['id' => $person->id]);
        $this->assertDatabaseMissing(\App\Models\Instructor::TABLE, [
            'id' => $instructor->id,
            'deleted_at' => null
        ]);
    }
}
