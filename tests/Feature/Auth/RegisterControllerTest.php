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
    public const VERIFY_URL = 'register/verify';

    private VerificationService $verificationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(TextMessagingService::class, static function (MockInterface $mock) {
            $mock->shouldReceive('send');
        });

        $this->verificationService = \app(VerificationService::class);
    }

    /**
     * @param string $userType
     * @dataProvider registerUserData
     */
    public function testRegisterUser(string $userType): void
    {
        Event::fake();

        $postData = [
            'phone' => $this->faker->e164PhoneNumber,
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
                'message' => 'validation_error',
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
                'message' => 'validation_error',
            ]);

        // Sending phone with correct user_type
        $postData['user_type'] = $userType;
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'verification_code_sent',
            ]);

        $this->assertDatabaseHas(VerificationCode::TABLE, [
            'phone_number' => $postData['phone']
        ]);

        // Sending phone with wrong verification code
        $postData['verification_code'] = 'wrong_verification_code';
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(409)
            ->assertJson([
                'error' => 'verification_code_is_invalid',
                'message' => 'Код введён неверно',
            ]);

        /** @var VerificationCode $verificationCode */
        $verificationCode = VerificationCode::query()
            ->where('phone_number', $postData['phone'])
            ->first();

        // Sending verified phone with code without any additional data
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(409)
            ->assertJson([
                'error' => 'verification_code_is_invalid',
                'message' => 'Код введён неверно',
            ]);

        // Sending phone with correct verification code
        $now = Carbon::now();
        $postData['verification_code'] = $verificationCode->verification_code;
        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(201)
            ->assertJson([
                'data' =>
                    [
                        'name' => "{$postData['first_name']} {$postData['last_name']}",
                        'username' => $postData['phone'],
                        'person' => [
                            'last_name' => $postData['last_name'],
                            'first_name' => $postData['first_name'],
                            'patronymic_name' => $postData['patronymic_name'],
                            'birth_date' => $postData['birth_date'],
                            'gender' => $postData['gender'],
                            'phone' => $postData['phone'],
                            'email' => $postData['email'],
                            'picture' => null,
                            'picture_thumb' => null,
                            'instagram_username' => null,
                            'telegram_username' => null,
                            'vk_uid' => null,
                            'vk_url' => null,
                            'facebook_uid' => null,
                            'facebook_url' => null,
                            'note' => null,
                            'customer' => $userType === Customer::class ? [] : null,
                            'student' => $userType === Student::class ? [] : null,
                            'instructor' => $userType === Instructor::class ? [] : null,
                            'is_customer' => $userType === Customer::class,
                            'is_student' => $userType === Student::class,
                            'is_instructor' => $userType === Instructor::class,
                            'created_at' => $now->toDateTimeString()
                        ],
                        'permissions' => [],
                        'created_at' => $now->toDateTimeString(),
                        'approved_at' => null,
                        'seen_at' => null
                    ]
            ]);

        $this->assertDatabaseHas(User::TABLE, [
            'name' => "{$postData['first_name']} {$postData['last_name']}",
            'username' => $postData['phone'],
            'created_at' => $now->toDateTimeString(),
        ]);

        $this->assertDatabaseHas(Person::TABLE, [
            'last_name' => $postData['last_name'],
            'first_name' => $postData['first_name'],
            'patronymic_name' => $postData['patronymic_name'],
            'birth_date' => $postData['birth_date'],
            'gender' => $postData['gender'],
            'phone' => $postData['phone'],
            'email' => $postData['email'],
            'created_at' => $now->toDateTimeString(),
        ]);

        $person = Person::query()->where([
            'last_name' => $postData['last_name'],
            'first_name' => $postData['first_name'],
            'patronymic_name' => $postData['patronymic_name'],
            'birth_date' => $postData['birth_date'],
            'gender' => $postData['gender'],
            'phone' => $postData['phone'],
            'email' => $postData['email'],
            'created_at' => $now->toDateTimeString(),
        ])->first();

        Event::assertDispatched(UserCreatedEvent::class);
        Event::assertDispatched(UserRegisteredEvent::class);

        switch ($userType) {
            case Student::class:
                $this->assertDatabaseHas(Student::TABLE, [
                    'name' => 'Dots Roman A.',
                    'person_id' => $person->id,
                    'created_at' => $now->toDateTimeString(),
                ]);
                Event::assertDispatched(StudentCreatedEvent::class);
                break;
            case Instructor::class:
                $this->assertDatabaseHas(Instructor::TABLE, [
                    'name' => 'Roman Dots',
                    'person_id' => $person->id,
                    'created_at' => $now->toDateTimeString(),
                ]);
                Event::assertDispatched(InstructorCreatedEvent::class);
                break;
            case User::class:
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
        $postData = [];
        $postData['phone'] = $this->faker->e164PhoneNumber;

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(422)
            ->assertJson([
                'data' => [
                    'user_type' => [
                        ['name' => 'required']
                    ]
                ],
                'message' => 'validation_error',
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
                'message' => 'validation_error',
            ]);

        $postData['user_type'] = $userType;

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'verification_code_sent',
            ]);

        $this->assertDatabaseHas(VerificationCode::TABLE, [
            'phone_number' => $postData['phone']
        ]);

        /** @var VerificationCode $verificationCode */
        $verificationCode = VerificationCode::query()
            ->where('phone_number', $postData['phone'])
            ->first();
        $postData['verification_code'] = $verificationCode->verification_code;

        $postData += $invalidData;

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
            ]);
    }

    public function registerUserData(): array
    {
        return [
            'User' => [User::class],
            'Student' => [Student::class],
            'Instructor' => [Instructor::class],
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
        $fakePerson = $this->createFakePerson();

        $postData = [
            'user_type' => Student::class,
            'phone' => $fakePerson->phone,
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
            ->assertStatus(200)
            ->assertJson([
                'message' => 'verification_code_sent',
            ]);

        /** @var VerificationCode $verificationCode */
        $verificationCode = VerificationCode::query()
            ->where('phone_number', $postData['phone'])
            ->first();
        $postData['verification_code'] = $verificationCode->verification_code;

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'person' => [
                        'id' => $fakePerson->id,
                        'phone' => $fakePerson->phone,
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
        $fakeUser = $this->createFakeUser();
        $fakePerson = $fakeUser->person;

        $postData = [
            'user_type' => Student::class,
            'phone' => $fakePerson->phone,
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
            ->assertStatus(200)
            ->assertJson([
                'message' => 'verification_code_sent',
            ]);

        /** @var VerificationCode $verificationCode */
        $verificationCode = VerificationCode::query()
            ->where('phone_number', $postData['phone'])
            ->first();
        $postData['verification_code'] = $verificationCode->verification_code;

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
        $postData = [
            'phone' => $this->faker->e164PhoneNumber,
            'email' => $this->faker->email,
            'last_name' => 'Dots',
            'first_name' => 'Roman',
            'patronymic_name' => 'A.',
            'birth_date' => '1986-01-08',
            'gender' => Person::GENDER_MALE,
        ];

        $fakePerson = $this->createFakePerson($postData);

        $postData['user_type'] = Student::class;
        $postData['password'] = '123456';

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'verification_code_sent',
            ]);

        /** @var VerificationCode $verificationCode */
        $verificationCode = VerificationCode::query()
            ->where('phone_number', $postData['phone'])
            ->first();
        $postData['verification_code'] = $verificationCode->verification_code;

        $this
            ->post(self::REGISTER_URL, $postData)
            ->assertStatus(409)
            ->assertJson([
                'error' => 'user_already_registered_with_another_phone_number',
                'message' => 'Пользователь уже зарегистрирован с другим номером телефона',
            ]);
    }

    public function testCheckVerificationCode(): void
    {
        $phone = $this->faker->e164PhoneNumber;
        $this->verificationService->verifyPhoneNumber($phone);

        $this->assertDatabaseHas(VerificationCode::TABLE, [
            'phone_number' => $phone
        ]);

        /** @var VerificationCode $verificationCode */
        $verificationCode = VerificationCode::query()->where('phone_number', $phone)->first();
        $code = $verificationCode->verification_code;

        $this
            ->post(self::VERIFY_URL, ['phone' => $phone, 'verification_code' => 'wrong_code'])
            ->assertStatus(409)
            ->assertJson([
                'error' => 'verification_code_is_invalid',
                'message' => 'Код введён неверно',
            ]);

        $this
            ->post(self::VERIFY_URL, ['phone' => $phone, 'verification_code' => $code])
            ->assertStatus(200);
    }
}
