<?php

namespace App\Tests;

use App\Facades\EmailTwoFactorAuthorization;
use App\Facades\OtpTwoFactorAuthorization;
use App\Facades\SmsTwoFactorAuthorization;
use App\Facades\TokenGenerator;
use App\Mails\ForgotPasswordMail;
use App\Mails\AccountConfirmationMail;
use App\Mails\TwoFactorAuthenticationMail;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthTest extends TestCase
{
    protected $admin;
    protected $user;
    protected $notVerifiedUser;
    protected $userWithPhoneAuth;
    protected $userWithOtpAuth;
    protected $softDeletedUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->notVerifiedUser = User::find(3);
        $this->userWithPhoneAuth = User::find(4);
        $this->userWithOtpAuth = User::find(5);
        $this->softDeletedUser = User::withTrashed()->find(7);
    }

    public function testRegister()
    {
        $data = $this->getJsonFixture('new_user.json');

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_OK);

        Arr::forget($data, ['password', 'confirm']);

        $this->assertDatabaseHas('users', $data);
    }

    public function testRegisterExistedUsername()
    {
        $data = $this->getJsonFixture('new_user.json');
        $data['username'] = 'Gerhard Feest';

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegisterExistedTagname()
    {
        $data = $this->getJsonFixture('new_user.json');
        $data['tagname'] = 'Gerhard Feest';

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegisterCheckVerificationToken()
    {
        TokenGenerator::shouldReceive('getRandom')->andReturn('test_token')->once();

        $data = $this->getJsonFixture('new_user.json');

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'id' => User::max('id'),
            'email_verified_at' => null,
            'email_verification_token' => 'test_token',
            'email_verification_token_sent_at' => Carbon::now()
        ]);
    }

    public function testRegisterWithRedirect()
    {
        TokenGenerator::shouldReceive('getRandom')
            ->andReturn('test_token')
            ->once();

        $data = $this->getJsonFixture('new_user.json');
        $data['redirect_after_verification'] = '/profile/promocodes';

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertMailEquals(AccountConfirmationMail::class, [
            'emails' => $data['email'],
            'fixture' => 'confirm_email_with_redirect.html',
            'subject' => 'Account verification'
        ]);
    }

    public function testRegisterBySoftDeletedAccount()
    {
        $data = $this->getJsonFixture('new_user.json');
        $data['email'] = 'soft.deleted.user@email.com';

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testRegisterCheckPassword()
    {
        $data = $this->getJsonFixture('new_user.json');

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(Hash::check($data['password'], User::find(User::max('id'))->password));
    }

    public function testRegisterCheckConfirmEmail()
    {
        TokenGenerator::shouldReceive('getRandom')
            ->andReturn('test_token')
            ->once();

        $data = $this->getJsonFixture('new_user.json');

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertMailEquals(AccountConfirmationMail::class, [
            'emails' => $data['email'],
            'fixture' => 'confirm_email.html',
            'subject' => 'Account verification'
        ]);
    }

    public function testRegisterWithNotVerifiedEmail()
    {
        $data = $this->getJsonFixture('new_user.json');
        $data['email'] = 'not.verified@email.com';
        $existedUserId = User::where('email', $data['email'])->first()->id;

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_OK);

        $data['id'] = $existedUserId;
        Arr::forget($data, ['password', 'confirm']);

        $this->assertDatabaseHas('users', $data);
    }

    public function testRegisterWithConfirmedEmail()
    {
        $data = $this->getJsonFixture('new_user.json');

        $data['email'] = 'user@example.com';

        $response = $this->json('post', '/auth/register', $data);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testVerifyEmail()
    {
        $response = $this->json('post', '/auth/register/email/verify', [
            'token' => 'correct_confirmation_code'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertArrayHasKey('token', $response->json());
        $this->assertNotEmpty($response->json('token'));
    }

    public function testVerifyEmailNotFound()
    {
        $response = $this->json('post', '/auth/register/email/verify', [
            'token' => 'not_existed_code'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testVerifyEmailWithOldToken()
    {
        $response = $this->json('post', '/auth/register/email/verify', [
            'token' => 'old_confirmation_code'
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testVerifyEmailCheckDB()
    {
        $response = $this->json('post', '/auth/register/email/verify', [
            'token' => 'correct_confirmation_code'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'id' => 3,
            'email_verification_token' => null,
            'email_verification_token_sent_at' => null,
            'email_verified_at' => Carbon::now()
        ]);
    }

    public function testVerifyEmailWrongConfirmationCode()
    {
        $response = $this->json('post', '/auth/register/email/verify', [
            'token' => 'wrong_confirmation_code'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLogin()
    {
        EmailTwoFactorAuthorization::shouldReceive('send')->with('admin@example.com');

        $response = $this->json('post', '/auth/login', [
            'login' => 'admin@example.com',
            'password' => 'correct_password'
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testLoginByCapitalizedName()
    {
        EmailTwoFactorAuthorization::shouldReceive('send')->with('admin@example.com');

        $response = $this->json('post', '/auth/login', [
            'login' => 'aDmIn@example.com',
            'password' => 'correct_password'
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testLoginCheckEmail()
    {
        TokenGenerator::shouldReceive('getCode')->andReturn('123456');

        $response = $this->json('post', '/auth/login', [
            'login' => 'admin@example.com',
            'password' => 'correct_password'
        ]);

        $response->assertOk();

        $this->assertMailEquals(TwoFactorAuthenticationMail::class, [
            'emails' => 'admin@example.com',
            'fixture' => 'email_login_2fa.html',
            'subject' => 'ARTfora. 2FA code'
        ]);
    }

    public function testLoginCheckDB()
    {
        TokenGenerator::shouldReceive('getCode')->andReturn('123456');

        $response = $this->json('post', '/auth/login', [
            'login' => 'admin@example.com',
            'password' => 'correct_password'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('two_factor_auth_emails', [
            'email' => 'admin@example.com',
            'code' => '123456'
        ]);
    }

    public function testLoginWrongCredentials()
    {
        $response = $this->json('post', '/auth/login', [
            'login' => 'admin@example.com',
            'password' => 'wrong password'
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginAsNotVerifiedAccount()
    {
        $response = $this->json('post', '/auth/login', [
            'login' => 'not.verified@email.com',
            'password' => 'correct_password'
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }

    public function testLoginWithSms2fa()
    {
        SmsTwoFactorAuthorization::shouldReceive('verify')->andReturn('test_2fa_id')->once();

        $response = $this->json('post', '/auth/login', [
            'login' => 'user.sms.2fa@email.com',
            'password' => 'correct_password'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals([
            'message' => 'Two factor verification required. Code has been sent',
            'type' => User::SMS_2FA_TYPE,
            'user_id' => 4
        ], $response->json());
    }

    public function testLoginWithOtp2fa()
    {
        $response = $this->json('post', '/auth/login', [
            'login' => 'user.otp.2fa@email.com',
            'password' => 'correct_password'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals([
            'message' => 'Two factor verification required. Please use authorization application',
            'type' => User::OTP_2FA_TYPE,
            'user_id' => 5
        ], $response->json());
    }

    public function testCheckSms2faCode()
    {
        SmsTwoFactorAuthorization::shouldReceive('check')
            ->with($this->userWithPhoneAuth->phone, '123456')
            ->andReturn(true)
            ->once();

        $response = $this->json('post', '/auth/2fa/sms/check', [
            'code' => '123456',
            'phone' => $this->userWithPhoneAuth->phone
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertArrayHasKey('token', $response->json());
        $this->assertNotEmpty($response->json('token'));
    }

    public function testCheckSms2faCodeWithoutSms2faEnabled()
    {
        $response = $this->json('post', '/auth/2fa/sms/check', [
            'code' => '123456',
            'phone' => $this->admin->phone
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCheckSms2faCodeWrongCode()
    {
        SmsTwoFactorAuthorization::shouldReceive('check')
            ->with($this->userWithPhoneAuth->phone, 'wrong_code')
            ->andReturn(false)
            ->once();

        $response = $this->json('post', '/auth/2fa/sms/check', [
            'code' => 'wrong_code',
            'phone' => $this->userWithPhoneAuth->phone
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testCheckOtp2fa()
    {
        OtpTwoFactorAuthorization::shouldReceive('check')
            ->with('secret', '123456')
            ->andReturn(true)
            ->once();

        $response = $this->json('post', '/auth/2fa/otp/check', [
            'code' => '123456',
            'user_id' => $this->userWithOtpAuth->id
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertArrayHasKey('token', $response->json());
        $this->assertNotEmpty($response->json('token'));
    }

    public function testCheckOtp2faWrongCode()
    {
        OtpTwoFactorAuthorization::shouldReceive('check')
            ->with('secret', 'wrong_code')
            ->andReturn(false)
            ->once();

        $response = $this->json('post', '/auth/2fa/otp/check', [
            'code' => 'wrong_code',
            'user_id' => $this->userWithOtpAuth->id
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testCheckEmail2fa()
    {
        $response = $this->json('post', '/auth/2fa/email/check', [
            'code' => '123456',
            'email' => $this->user->email
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertArrayHasKey('token', $response->json());
        $this->assertNotEmpty($response->json('token'));
    }

    public function testCheckEmail2faWrongCode()
    {
        $response = $this->json('post', '/auth/2fa/email/check', [
            'code' => '098877',
            'email' => $this->user->email
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testRefreshToken()
    {
        $response = $this->actingAs($this->user)->json('get', '/auth/refresh');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertNotEmpty(
            $response->headers->get('authorization')
        );

        $auth = $response->headers->get('authorization');

        $explodedHeader = explode(' ', $auth);

        $this->assertNotEquals($this->jwt, last($explodedHeader));
    }

    public function testForgotPassword()
    {
        TokenGenerator::shouldReceive('getRandom')
            ->andReturn('some_token')
            ->once();

        $response = $this->json('post', '/auth/forgot-password', [
            'login' => $this->user->email
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('password_resets', [
            'email' => $this->user->email,
            'token' => 'some_token',
            'created_at' => Carbon::now()
        ]);

        $this->assertMailEquals(ForgotPasswordMail::class, [
            'emails' => $this->user->email,
            'fixture' => 'forgot_password_email.html'
        ]);
    }

    public function testForgotPasswordUserDoesNotExists()
    {
        $response = $this->json('post', '/auth/forgot-password', [
            'login' => 'not_exists@example.com'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testForgotPasswordEmailNotConfirmed()
    {
        $response = $this->json('post', '/auth/forgot-password', [
            'login' => 'not.confirmed@email.com'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRestorePassword()
    {
        $response = $this->json('post', '/auth/restore-password', [
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
            'token' => 'restore_token',
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', [
            'email' => 'restore@email.com',
            'password' => 'old_password'
        ]);

        $this->assertDatabaseMissing('password_resets', [
            'token' => 'restore_token'
        ]);
    }

    public function testRestorePasswordCheckPassword()
    {
        $response = $this->json('post', '/auth/restore-password', [
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
            'token' => 'restore_token',
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertTrue(Hash::check('new_password', User::find(6)->password));
    }

    public function testRestorePasswordWrongToken()
    {
        $response = $this->json('post', '/auth/restore-password', [
            'password' => 'new_password',
            'token' => 'incorrect_token',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCheckRestorePasswordToken()
    {
        $response = $this->json('post', '/auth/restore-password/check', [
            'token' => 'restore_token',
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testCheckRestorePasswordWrongToken()
    {
        $response = $this->json('post', '/auth/restore-password/check', [
            'token' => 'wrong_token',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testResend2faSms()
    {
        SmsTwoFactorAuthorization::shouldReceive('verify')
            ->with($this->userWithPhoneAuth->phone)
            ->once();

        $response = $this->json('post', '/auth/2fa/sms/resend', [
            'phone' => $this->userWithPhoneAuth->phone,
        ]);

        $response->assertOk();
    }

    public function testResend2faSmsNotEnabled()
    {
        $response = $this->json('post', '/auth/2fa/sms/resend', [
            'phone' => $this->userWithOtpAuth,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testResend2faSmsOfSoftDeletedUser()
    {
        $response = $this->json('post', '/auth/2fa/sms/resend', [
            'phone' => $this->softDeletedUser->phone,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testResend2faSmsPhoneNotFound()
    {
        $response = $this->json('post', '/auth/2fa/sms/resend', [
            'phone' => '0000000',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testResend2faEmail()
    {
        $response = $this->json('post', '/auth/2fa/email/resend', [
            'email' => $this->admin->email,
        ]);

        $response->assertOk();
    }

    public function testResend2faEmailCheckEmail()
    {
        TokenGenerator::shouldReceive('getCode')->andReturn('123456');

        $this->json('post', '/auth/2fa/email/resend', [
            'email' => $this->admin->email,
        ]);

        $this->assertMailEquals(TwoFactorAuthenticationMail::class, [
            'emails' => 'admin@example.com',
            'fixture' => 'email_login_2fa.html',
            'subject' => 'ARTfora. 2FA code'
        ]);
    }

    public function testResend2faEmailNotEnabled()
    {
        $response = $this->json('post', '/auth/2fa/email/resend', [
            'email' => $this->userWithPhoneAuth->email
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testResend2faEmailOfSoftDeletedUser()
    {
        $response = $this->json('post', '/auth/2fa/email/resend', [
            'email' => $this->softDeletedUser->email
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testResend2faEmailNotFound()
    {
        $response = $this->json('post', '/auth/2fa/email/resend', [
            'email' => 'not.exists@email.com',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testClearPasswordResets()
    {
        $this->artisan('password_resets:clear');

        $this->assertDatabaseHas('password_resets', [
            'token' => 'restore_token'
        ]);

        $this->assertDatabaseMissing('password_resets', [
            'token' => 'old_token'
        ]);
    }

    public function testEnable2faWithSms()
    {
        SmsTwoFactorAuthorization::shouldReceive('verify')->once();

        $response = $this->actingAs($this->user)->json('post', '/auth/2fa/sms/enable');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testEnable2faWithSmsAsUserWithoutPhone()
    {
        $response = $this->actingAs($this->userWithOtpAuth)->json('post', '/auth/2fa/sms/enable');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testEnable2faWithSmsAsNotVerifiedUser()
    {
        $response = $this->actingAs($this->notVerifiedUser)->json('post', '/auth/2fa/sms/enable');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testEnable2faWithSmsNoAuth()
    {
        $response = $this->json('post', '/auth/2fa/sms/enable');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetOtpQrCode()
    {
        OtpTwoFactorAuthorization::shouldReceive('generate')->andReturn([
            'secret' => 'secret',
            'qr_code' => 'test-url'
        ])->once();

        $response = $this->actingAs($this->user)->json('post', '/auth/2fa/otp/generate');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals([
            'secret' => 'secret',
            'qr_code' => 'test-url'
        ], $response->json());

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'otp_secret' => 'secret'
        ]);
    }

    public function testGetOtpQrCodeAsNotVerifiedUser()
    {
        $response = $this->actingAs($this->notVerifiedUser)->json('post', '/auth/2fa/otp/generate');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testGetOtpQrCodeNoAuth()
    {
        $response = $this->json('post', '/auth/2fa/otp/generate');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testConfirm2faWithSms()
    {
        SmsTwoFactorAuthorization::shouldReceive('check')->andReturn(true)->once();

        $response = $this->actingAs($this->user)->json('post', '/auth/2fa/sms/confirm', [
            'code' => 'right_code'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            '2fa_type' => User::SMS_2FA_TYPE
        ]);
    }

    public function testConfirm2faWithSmsWrongCode()
    {
        SmsTwoFactorAuthorization::shouldReceive('check')->andReturn(false)->once();

        $response = $this->actingAs($this->user)->json('post', '/auth/2fa/sms/confirm', [
            'code' => 'right_code'
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testConfirm2faWithSmsWithoutPhone()
    {
        $response = $this->actingAs($this->userWithOtpAuth)->json('post', '/auth/2fa/sms/confirm', [
            'code' => 'right_code'
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testConfirm2faWithSmsNoAuth()
    {
        $response = $this->json('post', '/auth/2fa/sms/confirm');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testEnableOtp2fa()
    {
        OtpTwoFactorAuthorization::shouldReceive('check')->andReturn(true)->once();

        $response = $this->actingAs($this->user)->json('post', '/auth/2fa/otp/confirm', [
            'code' => 'right_code'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            '2fa_type' => User::OTP_2FA_TYPE
        ]);
    }

    public function testEnableOtp2faWrongCode()
    {
        OtpTwoFactorAuthorization::shouldReceive('check')->andReturn(false)->once();

        $response = $this->actingAs($this->user)->json('post', '/auth/2fa/otp/confirm', [
            'code' => 'right_code'
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testEnableOtp2faCodeNoAuth()
    {
        $response = $this->json('post', '/auth/2fa/otp/confirm');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
