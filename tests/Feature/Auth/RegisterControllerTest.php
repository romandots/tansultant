<?php
/**
 * File: RegisterControllerTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Events\InstructorCreatedEvent;
use App\Events\StudentCreatedEvent;
use App\Events\UserCreatedEvent;
use App\Events\UserRegisteredEvent;
use App\Models\Customer;
use App\Models\Instructor;
use App\Models\Person;
use App\Models\Student;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\TextMessaging\TextMessagingService;
use App\Services\Verify\VerificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

class RegisterControllerTest extends TestCase
{
    use CreatesFakeUser;
    use CreatesFakePerson;
    use WithFaker;

    public const REGISTER_URL = 'register';

    private VerificationService $verificationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(TextMessagingService::class, static function (MockInterface $mock) {
            $mock->shouldReceive('send');
        });

        $this->verificationService = \app(VerificationService::class);
    }

    private function createFakeVerificationCode(?string $phone = null, ?string $code = null): VerificationCode
    {
        return \factory(\App\Models\VerificationCode::class)->create([
            'id' => \uuid(),
            'phone_number' => $phone ?? $this->faker->e164PhoneNumber,
            'verification_code' => $code ?? $this->faker->numerify('####'),
            'created_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addMinute(),
            'verified_at' => Carbon::now()->addSeconds(30),
        ]);
    }

    /**
     * @param string $userType
     * @dataProvider registerUserData
     */
    public function testRegisterUser(string $userType): void
    {
        Event::fake();

        $phoneNumber = $this->faker->e164PhoneNumber;
        $verificationCode = $this->createFakeVerificationCode($phoneNumber, '7777');

        $postData = [
            'verification_code_id' => $verificationCode->id,
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => Person::GENDER_MALE,
            'password' => '123456',
        ];

        // Sending phone without user_type
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(422)
            ->assertJson([
                'data' => [
                    'user_type' => [
                        ['name' => 'required']
                    ]
                ],
                'error' => 'validation_error',
                'message' => 'Ошибка валидации',
            ]);

        // Sending phone with wrong user_type
        $postData['user_type'] = 'wrong_user_type';
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(422)
            ->assertJson([
                'data' => [
                    'user_type' => [
                        ['name' => 'in']
                    ]
                ],
                'error' => 'validation_error',
                'message' => 'Ошибка валидации',
            ]);

        // Sending phone with correct verification code
        $now = Carbon::now();
        $postData['user_type'] = $userType;
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(201)
            ->assertJson([
                'data' =>
                    [
                        'name' => "{$postData['first_name']} {$postData['last_name']}",
                        'username' => $phoneNumber,
                        'person' => [
                            'last_name' => $postData['last_name'],
                            'first_name' => $postData['first_name'],
                            'patronymic_name' => $postData['patronymic_name'],
                            'birth_date' => $postData['birth_date'],
                            'gender' => $postData['gender'],
                            'phone' => $phoneNumber,
                            'email' => $postData['email'],
                            'picture' => null,
                            'picture_thumb' => null,
                            'instagram_username' => null,
                            'telegram_username' => null,
                            'vk_uid' => null,
                            'facebook_uid' => null,
                            'note' => null,
                            'is_customer' => $userType === \base_classname(Customer::class),
                            'is_student' => $userType === \base_classname(Student::class),
                            'is_instructor' => $userType === \base_classname(Instructor::class),
                            'created_at' => $now->toDateTimeString(),
                        ],
                        'permissions' => [],
                        'created_at' => $now->toDateTimeString(),
                        'updated_at' => $now->toDateTimeString(),
                        'approved_at' => $userType === \base_classname(Student::class) ? $now->toDateTimeString() : null,
                        'seen_at' => null,
                    ]
            ]);

        $this->assertDatabaseHas(User::TABLE, [
            'name' => "{$postData['first_name']} {$postData['last_name']}",
            'username' => $phoneNumber,
            'created_at' => $now->toDateTimeString(),
        ]);

        $this->assertDatabaseHas(Person::TABLE, [
            'last_name' => $postData['last_name'],
            'first_name' => $postData['first_name'],
            'patronymic_name' => $postData['patronymic_name'],
            'birth_date' => $postData['birth_date'],
            'gender' => $postData['gender'],
            'phone' => $phoneNumber,
            'email' => $postData['email'],
            'created_at' => $now->toDateTimeString(),
        ]);

        $person = Person::query()->where([
            'last_name' => $postData['last_name'],
            'first_name' => $postData['first_name'],
            'patronymic_name' => $postData['patronymic_name'],
            'birth_date' => $postData['birth_date'],
            'gender' => $postData['gender'],
            'phone' => $phoneNumber,
            'email' => $postData['email'],
            'created_at' => $now->toDateTimeString(),
        ])->first();

        Event::assertDispatched(UserCreatedEvent::class);
        Event::assertDispatched(UserRegisteredEvent::class);

        switch ($userType) {
            case \base_classname(Student::class):
                $this->assertDatabaseHas(Student::TABLE, [
                    'name' => 'Dots Roman A.',
                    'person_id' => $person->id,
                    'created_at' => $now->toDateTimeString(),
                ]);
                Event::assertDispatched(StudentCreatedEvent::class);
                break;
            case \base_classname(Instructor::class):
                $this->assertDatabaseHas(Instructor::TABLE, [
                    'name' => 'Roman Dots',
                    'person_id' => $person->id,
                    'created_at' => $now->toDateTimeString(),
                ]);
                Event::assertDispatched(InstructorCreatedEvent::class);
                break;
            case \base_classname(User::class):
                break;
            default:
                throw new \LogicException('Unsupported user type');
        }
    }

    /**
     * @param string $userType
     * @param array $invalidData
     * @dataProvider registerUserInvalidData
     */
    public function testRegisterUserInvalidData(string $userType, array $invalidData): void
    {
        $phoneNumber = $this->faker->e164PhoneNumber;
        $verificationCode = $this->createFakeVerificationCode($phoneNumber, '7777');

        $postData = [];
        $postData['verification_code_id'] = $verificationCode->id;

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(422)
            ->assertJson([
                'data' => [
                    'user_type' => [
                        ['name' => 'required']
                    ]
                ],
                'error' => 'validation_error',
                'message' => 'Ошибка валидации',
            ]);

        $postData['user_type'] = 'wrong_user_type';
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(422)
            ->assertJson([
                'data' => [
                    'user_type' => [
                        ['name' => 'in']
                    ]
                ],
                'error' => 'validation_error',
                'message' => 'Ошибка валидации',
            ]);

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(422)
            ->assertJson([
                'error' => 'validation_error',
                'message' => 'Ошибка валидации',
            ]);
    }

    public function registerUserData(): array
    {
        return [
            'User' => ['User'],
            'Student' => ['Student'],
            'Instructor' => ['Instructor'],
        ];
    }

    public function registerUserInvalidData(): array
    {
        return [
            [
                User::class,
                [
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => Person::GENDER_MALE,
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => Person::GENDER_MALE,
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'first_name' => 'Roman',
                    'birth_date' => '1986-01-08',
                    'gender' => Person::GENDER_MALE,
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'gender' => Person::GENDER_MALE,
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => 'it',
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => Person::GENDER_MALE,
                ]
            ],
            [
                Student::class,
                [
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => Person::GENDER_MALE,
                    'password' => '123456',
                ]
            ],
            [
                Instructor::class,
                [
                    'last_name' => 'Dots',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => Person::GENDER_MALE,
                    'password' => '123456',
                ]
            ],
        ];
    }

    public function testRegisterWithExistingPersonByPhoneNumber(): void
    {
        $phoneNumber = $this->faker->e164PhoneNumber;
        $normalizedPhone = \normalize_phone_number($phoneNumber);
        $fakePerson = $this->createFakePerson(['phone' => $normalizedPhone]);
        $verificationCode = $this->createFakeVerificationCode($phoneNumber, '7777');

        $this->assertDatabaseHas(Person::TABLE, ['phone' => $normalizedPhone]);

        $postData = [
            'user_type' => \base_classname(Student::class),
            'verification_code_id' => $verificationCode->id,
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => Person::GENDER_MALE,
            'password' => '123456',
        ];

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'person' => [
                        'id' => $fakePerson->id,
                        'phone' => $normalizedPhone,
                        'last_name' => 'Dots',
                        'first_name' => 'Roman',
                        'patronymic_name' => 'A.',
                        'birth_date' => '1986-01-08',
                        'gender' => Person::GENDER_MALE,
                    ]
                ],
            ]);
    }

    public function testRegisterWithExistingUserByPhoneNumber(): void
    {
        $phoneNumber = $this->faker->e164PhoneNumber;
        $normalizedPhone = \normalize_phone_number($phoneNumber);
        $verificationCode = $this->createFakeVerificationCode($normalizedPhone, '7777');

        $fakeUser = $this->createFakeUser();
        $fakePerson = $fakeUser->person;
        $fakePerson->phone = $normalizedPhone;
        $fakePerson->save();

        $postData = [
            'user_type' => \base_classname(Student::class),
            'verification_code_id' => $verificationCode->id,
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => Person::GENDER_MALE,
            'password' => '123456',
        ];

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(409)
            ->assertJson([
                'error' => 'user_with_this_phone_number_already_registered',
                'message' => 'Пользователь уже зарегистрирован. Воспользуйтесь восстановлением пароля'
            ]);
    }

    public function testRegisterWithExistingNameAndBirthDate(): void
    {
        $phoneNumber = $this->faker->e164PhoneNumber;
        $normalizedPhone = \normalize_phone_number($phoneNumber);
        $verificationCode = $this->createFakeVerificationCode($normalizedPhone, '7777');

        $postData = [
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => Person::GENDER_MALE,
        ];

        $fakePerson = $this->createFakePerson($postData);

        $postData['verification_code_id'] = $verificationCode->id;
        $postData['user_type'] = \base_classname(Student::class);
        $postData['password'] = '123456';

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(409)
            ->assertJson([
                'error' => 'user_already_registered_with_another_phone_number',
                'message' => 'Пользователь уже зарегистрирован с другим номером телефона',
            ]);
    }
}
