<?php
/**
 * File: RegisterControllerTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Events\Instructor\InstructorCreatedEvent;
use App\Events\Student\StudentCreatedEvent;
use App\Events\User\UserCreatedEvent;
use App\Events\User\UserRegisteredEvent;
use App\Models\Enum\UserType;
use App\Models\Instructor;
use App\Models\Person;
use App\Models\Student;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\TextMessaging\TextMessagingService;
use App\Services\Verification\VerificationService;
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

    protected function createFakeVerificationCode(?string $phone = null, ?string $code = null): VerificationCode
    {
        return \App\Models\VerificationCode::factory()->create([
            'id' => \uuid(),
            'phone_number' => $phone ?? $this->faker->phoneNumber,
            'verification_code' => $code ?? $this->faker->numerify('####'),
            'created_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addMinute(),
            'verified_at' => Carbon::now()->addSeconds(30),
        ]);
    }

    /**
     * @param UserType $userType
     * @dataProvider registerUserData
     */
    public function testRegisterUser(UserType $userType): void
    {
        Event::fake();

        $this->freezeTime(function(Carbon $carbon) use ($userType) {

            $phoneNumber = $this->faker->phoneNumber;
            $verificationCode = $this->createFakeVerificationCode($phoneNumber, '7777');

            $postData = [
                'verification_code_id' => $verificationCode->id,
                'email' => $this->faker->email,
                'last_name' => 'Dots',
                'first_name' => 'Roman',
                'patronymic_name' => 'A.',
                'birth_date' => '1986-01-08',
                'gender' => \App\Models\Enum\Gender::MALE->value,
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
            $now = $carbon::now();
            $postData['user_type'] = $userType->value;
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
                                'phone' => \phone_format($phoneNumber),
                                'email' => $postData['email'],
                                'picture' => null,
                                'picture_thumb' => null,
                                'instagram_username' => null,
                                'telegram_username' => null,
                                'vk_uid' => null,
                                'facebook_uid' => null,
                                'note' => null,
                                'is_customer' => $userType === UserType::CUSTOMER,
                                'is_student' => $userType === UserType::STUDENT,
                                'is_instructor' => $userType === UserType::INSTRUCTOR,
                                'created_at' => $now->toDateTimeString(),
                            ],
                            'permissions' => [],
                            'created_at' => $now->toDateTimeString(),
                            'updated_at' => $now->toDateTimeString(),
                            'approved_at' => $userType === UserType::STUDENT ? $now->toDateTimeString() : null,
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
                case UserType::STUDENT:
                    $this->assertDatabaseHas(Student::TABLE, [
                        'name' => 'Dots Roman A.',
                        'person_id' => $person->id,
                        'created_at' => $now->toDateTimeString(),
                    ]);
                    Event::assertDispatched(StudentCreatedEvent::class);
                    break;
                case UserType::INSTRUCTOR:
                    $this->assertDatabaseHas(Instructor::TABLE, [
                        'name' => 'Roman Dots',
                        'person_id' => $person->id,
                        'created_at' => $now->toDateTimeString(),
                    ]);
                    Event::assertDispatched(InstructorCreatedEvent::class);
                    break;
                case UserType::USER:
                    break;
                default:
                    throw new \LogicException('Unsupported user type');
            }
        });
    }

    /**
     * @param string $userType
     * @param array $invalidData
     * @dataProvider registerUserInvalidData
     */
    public function testRegisterUserInvalidData(string $userType, array $invalidData): void
    {
        $phoneNumber = $this->faker->phoneNumber;
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
            'User' => [UserType::USER],
            'Student' => [UserType::STUDENT],
            'Instructor' => [UserType::INSTRUCTOR],
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
                    'gender' => \App\Models\Enum\Gender::MALE->value,
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => \App\Models\Enum\Gender::MALE->value,
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'first_name' => 'Roman',
                    'birth_date' => '1986-01-08',
                    'gender' => \App\Models\Enum\Gender::MALE->value,
                    'password' => '123456',
                ]
            ],
            [
                User::class,
                [
                    'last_name' => 'Dots',
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'gender' => \App\Models\Enum\Gender::MALE->value,
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
                    'gender' => \App\Models\Enum\Gender::MALE->value,
                ]
            ],
            [
                Student::class,
                [
                    'first_name' => 'Roman',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => \App\Models\Enum\Gender::MALE->value,
                    'password' => '123456',
                ]
            ],
            [
                Instructor::class,
                [
                    'last_name' => 'Dots',
                    'patronymic_name' => 'A.',
                    'birth_date' => '1986-01-08',
                    'gender' => \App\Models\Enum\Gender::MALE->value,
                    'password' => '123456',
                ]
            ],
        ];
    }

    public function testRegisterWithExistingPersonByPhoneNumber(): void
    {
        $phoneNumber = $this->faker->phoneNumber;
        $normalizedPhone = \normalize_phone_number($phoneNumber);
        $fakePerson = $this->createFakePerson(['phone' => $normalizedPhone]);
        $verificationCode = $this->createFakeVerificationCode($phoneNumber, '7777');

        $this->assertDatabaseHas(Person::TABLE, ['phone' => $normalizedPhone]);

        $postData = [
            'user_type' => UserType::STUDENT->value,
            'verification_code_id' => $verificationCode->id,
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => \App\Models\Enum\Gender::MALE->value,
            'password' => '123456',
        ];

        Event::fake();

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'person' => [
                        'id' => $fakePerson->id,
                        'phone' => \phone_format($normalizedPhone),
                        'last_name' => 'Dots',
                        'first_name' => 'Roman',
                        'patronymic_name' => 'A.',
                        'birth_date' => '1986-01-08',
                        'gender' => \App\Models\Enum\Gender::MALE->value,
                    ]
                ],
            ]);

        Event::assertDispatched(UserRegisteredEvent::class, function (UserRegisteredEvent $event) {
            return $event->getUser() instanceof User;
        });
    }

    public function testRegisterWithExistingUserByPhoneNumber(): void
    {
        $phoneNumber = $this->faker->phoneNumber;
        $normalizedPhone = \normalize_phone_number($phoneNumber);
        $verificationCode = $this->createFakeVerificationCode($normalizedPhone, '7777');

        $fakeUser = $this->createFakeUser();
        $fakePerson = $fakeUser->person;
        $fakePerson->phone = $normalizedPhone;
        $fakePerson->save();

        $postData = [
            'user_type' => UserType::STUDENT->value,
            'verification_code_id' => $verificationCode->id,
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => \App\Models\Enum\Gender::MALE->value,
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
        $phoneNumber = $this->faker->phoneNumber;
        $normalizedPhone = \normalize_phone_number($phoneNumber);
        $verificationCode = $this->createFakeVerificationCode($normalizedPhone, '7777');

        $postData = [
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => \App\Models\Enum\Gender::MALE->value,
        ];

        $fakePerson = $this->createFakePerson($postData);

        $postData['verification_code_id'] = $verificationCode->id;
        $postData['user_type'] = UserType::STUDENT->value;
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
