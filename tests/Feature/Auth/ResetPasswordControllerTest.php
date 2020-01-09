<?php
/**
 * File: ResetPasswordControllerTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\VerificationCode;
use App\Services\TextMessaging\TextMessagingService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

class ResetPasswordControllerTest extends TestCase
{
    use CreatesFakeUser;
    use CreatesFakePerson;
    use WithFaker;

    public const URL = 'user/password/reset';

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(TextMessagingService::class, static function (MockInterface $mock) {
            $mock->shouldReceive('send');
        });

//        $this->mock(VerificationService::class, static function (MockInterface $mock) {
//            $mock->shouldReceive('verifyPhoneNumber');
//        });
    }

    public function testPasswordReset(): void
    {
        Event::fake();

        $phoneNumber = $this->faker->e164PhoneNumber;
        $normalizedPhoneNumber = \normalize_phone_number($phoneNumber);
        $user = $this->createFakeUser([
            'username' => $normalizedPhoneNumber
        ]);
        $originalPassword = $user->password;

        $postData = [
            'username' => $phoneNumber,
        ];

        // Request username verification
        $this
            ->post(self::URL, $postData)
            ->assertStatus(200);

        // Request again
        $this
            ->post(self::URL, $postData)
            ->assertStatus(400)
            ->assertJson([
                'error' => 'verification_code_was_sent_recently',
                'message' => 'Код был отправлен недавно. Подождите немного'
            ]);

        // Send wrong verification code
        $postData['verification_code'] = 'code';
        $this
            ->post(self::URL, $postData)
            ->assertStatus(409)
            ->assertJson([
                'error' => 'verification_code_is_invalid',
                'message' => 'Код введён неверно'
            ]);

        $this->assertDatabaseHas(VerificationCode::TABLE, [
            'phone_number' => $normalizedPhoneNumber
        ]);

        /** @var VerificationCode $verificationCode */
        $verificationCode = VerificationCode::query()
            ->where('phone_number', $normalizedPhoneNumber)
            ->first();
        $postData['verification_code'] = $verificationCode->verification_code;

        // Password is yet the same
        $user->refresh();
        $this->assertEquals($originalPassword, $user->password);

        // Send new password
        $this
            ->post(self::URL, $postData)
            ->assertStatus(200);

        // Password is new
        $user->refresh();
        $this->assertNotEquals($originalPassword, $user->password);
    }
}
