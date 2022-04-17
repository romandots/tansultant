<?php
/**
 * File: RegisterControllerTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\VerificationCode;
use App\Services\TextMessaging\TextMessagingService;
use App\Services\Verify\VerificationService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

class VerificationControllerTest extends TestCase
{
    use CreatesFakeUser;
    use CreatesFakePerson;
    use WithFaker;

    public const VERIFY_URL = 'verify';

    private VerificationService $verificationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(TextMessagingService::class, static function (MockInterface $mock) {
            $mock->shouldReceive('send');
        });

        $this->verificationService = \app(VerificationService::class);
    }

    public function testVerifyPhone(): void
    {
        $phone = $this->faker->phoneNumber;
        $normalizedPhone = \normalize_phone_number($phone);

        $this
            ->post(self::VERIFY_URL, ['phone' => $phone])
            ->assertStatus(201)
            ->assertJson([
                'status' => 'verification_code_sent',
                'message' => 'Код подтверждения отправлен',
            ]);

        $this->assertDatabaseHas(VerificationCode::TABLE, [
            'phone_number' => $normalizedPhone
        ]);

        $this
            ->post(self::VERIFY_URL, ['phone' => $phone])
            ->assertStatus(400)
            ->assertJson([
                'error' => 'verification_code_was_sent_recently',
                'message' => 'Код был отправлен недавно. Подождите немного',
            ]);

        for ($try = 1; $try <= (int)\config('verification.max_tries', 4); $try++) {
            /** @var VerificationCode $verificationCode */
            $verificationCode = VerificationCode::query()
                ->where('phone_number', $normalizedPhone)
                ->orderBy('created_at', 'desc')
                ->first();

            $verificationCode->created_at = $verificationCode
                ->created_at
                ->subSeconds((int)\config('verification.timeout', 60)+1);
            $verificationCode->expired_at = $verificationCode
                ->expired_at
                ->subSeconds((int)\config('verification.timeout', 60)+1);
            $verificationCode->save();

            $this
                ->post(self::VERIFY_URL, ['phone' => $phone])
                ->assertStatus(201)
                ->assertJson([
                    'status' => 'verification_code_sent',
                    'message' => 'Код подтверждения отправлен',
                ]);
        }

        $this
            ->post(self::VERIFY_URL, ['phone' => $phone])
            ->assertStatus(400)
            ->assertJson([
                'error' => 'verification_code_was_sent_too_many_times',
                'message' => 'Вы больше не можете отправить код подтверждения. Сделайте паузу',
            ]);

        $verificationCode = VerificationCode::query()
            ->where('phone_number', $normalizedPhone)
            ->orderBy('created_at', 'desc')
            ->first();
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
