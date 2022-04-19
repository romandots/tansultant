<?php
/**
 * File: StudentTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Services\Permissions\StudentsPermissions;
use Tests\Traits\CreatesFakes;

/**
 * Class PersonTest
 */
class StudentTest extends \Tests\TestCase
{
    use CreatesFakes;

    protected const URL = 'admin/students';

    protected const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'person',
            'customer',
            'card_number',
            'status',
            'status_label',
            'seen_at',
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
        $this->artisan('db:seed');
        $this->me = $this->createFakeManagerUser([], [
            StudentsPermissions::CREATE,
            StudentsPermissions::READ,
            StudentsPermissions::UPDATE,
            StudentsPermissions::DELETE
        ]);
    }

    public function testShowDenied(): void
    {
        $me = $this->createFakeUser();
        $student = $this->createFakeStudent(['status' => \App\Models\StudentStatus::POTENTIAL]);

        $url = self::URL . '/' . $student->id;

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
        $student = $this->createFakeStudent(['status' => \App\Models\StudentStatus::POTENTIAL]);

        $url = self::URL . '/' . $student->id;

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
        $student = $this->createFakeStudent(['status' => \App\Models\StudentStatus::POTENTIAL]);

        $url = self::URL . '/' . $student->id;

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
        $student = $this->createFakeStudent(['status' => \App\Models\StudentStatus::POTENTIAL]);
        $person = $student->person;

        $url = self::URL . '/' . $student->id;

        $this
            ->actingAs($this->me, 'api')
            ->get($url)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $student->id,
                    'name' => $student->name,
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
                        'facebook_uid' => $person->facebook_uid,
                        'note' => $person->note,
                        'created_at' => $person->created_at->toDateTimeString()
                    ],
                    'status' => $student->status,
                    'status_label' => \trans('student.status.' . $student->status),
                    'seen_at' => $student->seen_at->toDateTimeString(),
                    'created_at' => $student->created_at->toDateTimeString()
                ]
            ]);
    }

    public function testStore(): void
    {
        $data = [
            'card_number' => (string)$this->faker->randomNumber(4),
            'last_name' => $this->faker->lastName,
            'first_name' => $this->faker->firstName,
            'patronymic_name' => $this->faker->firstName,
            'birth_date' => $this->faker->date(),
            'gender' => 'male',
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'instagram_username' => $this->faker->word,
            'telegram_username' => $this->faker->word,
            'note' => 'Some testy note',
        ];

        $this
            ->actingAs($this->me, 'api')
            ->post(self::URL, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'card_number' => $data['card_number'],
                    'name' => "{$data['last_name']} {$data['first_name']} {$data['patronymic_name']}",
                    'person' =>
                        [
                            'last_name' => $data['last_name'],
                            'first_name' => $data['first_name'],
                            'patronymic_name' => $data['patronymic_name'],
                            'birth_date' => $data['birth_date'],
                            'gender' => 'male',
                            'phone' => \normalize_phone_number($data['phone']),
                            'email' => $data['email'],
                            'picture' => null,
                            'picture_thumb' => null,
                            'instagram_username' => $data['instagram_username'],
                            'telegram_username' => $data['telegram_username'],
                            'vk_uid' => null,
                            'facebook_uid' => null,
                            'note' => 'Some testy note',
                        ],
                    'status' => 'potential',
                    'status_label' => 'Потенциальный'
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

    public function testCreateFromPerson(): void
    {
        $person = $this->createFakePerson();
        $data = [
            'card_number' => (string)$this->faker->unique()->randomNumber(4),
            'person_id' => $person->id
        ];
        $url = self::URL . '/from_person';

        $this
            ->actingAs($this->me, 'api')
            ->post($url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'name' => "{$person->last_name} {$person->first_name} {$person->patronymic_name}",
                    'card_number' => $data['card_number'],
                    'status' => \App\Models\StudentStatus::POTENTIAL,
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
                        'facebook_uid' => $person->facebook_uid,
                        'note' => $person->note,
                        'created_at' => $person->created_at->toDateTimeString()
                    ]
                ]
            ]);
    }

    public function testUpdate(): void
    {
        $oldCardNumber = $this->faker->numerify('#####');
        $newCardNumber = $this->faker->numerify('#####');

        $data = ['card_number' => $oldCardNumber];

        $student = $this->createFakeStudent(['card_number' => $newCardNumber]);
        $person = $student->person;

        $url = self::URL . '/' . $student->id;

        $this
            ->actingAs($this->me, 'api')
            ->put($url, $data)
            ->assertStatus(200)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'card_number' => $oldCardNumber,
                    'id' => $student->id,
                    'name' => $student->name,
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
                        'facebook_uid' => $person->facebook_uid,
                        'note' => $person->note,
                        'created_at' => $person->created_at->toDateTimeString()
                    ],
                    'status' => $student->status,
                    'status_label' => \trans('student.status.' . $student->status),
                    'seen_at' => $student->seen_at->toDateTimeString(),
                    'created_at' => $student->created_at->toDateTimeString()
                ]
            ]);
    }

    public function testDestroy(): void
    {
        $student = $this->createFakeStudent();
        $person = $student->person;

        $url = self::URL . '/' . $student->id;

        $this
            ->actingAs($this->me, 'api')
            ->delete($url)
            ->assertOk();

        $this->assertDatabaseHas(\App\Models\Person::TABLE, ['id' => $person->id]);
        $this->assertDatabaseMissing(\App\Models\Student::TABLE, [
            'id' => $student->id,
            'deleted_at' => null
        ]);
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
            [
                [
                    'card_number' => '',
                ]
            ],
        ];
    }

}
