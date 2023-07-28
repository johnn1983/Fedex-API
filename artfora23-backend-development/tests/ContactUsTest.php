<?php

namespace App\Tests;

use App\Facades\MtCaptcha;
use App\Mails\CommissionRequestMail;
use App\Mails\ContactUsMail;
use App\Services\SettingService;
use App\Tests\Support\MockClassTrait;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ContactUsTest extends TestCase
{
    use MockClassTrait;

    protected string $adminEmail;

    public function setUp(): void
    {
        parent::setUp();

        $this->adminEmail = app(SettingService::class)->get('contact_us.email');
    }

    public function testContactUsRequest()
    {
        MtCaptcha::shouldReceive('verify')->andReturn(true);

        $data = $this->getJsonFixture('contact_us_request.json');

        $response = $this->post('/contact-us', $data);

        $response->assertOk();
    }

    public function testContactUsRequestCatchaFailed()
    {
        Http::shouldReceive('get')
            ->with('https://service.mtcaptcha.com/mtcv1/api/checktoken', [
                'privatekey' => env('MTCAPTCHA_PRIVATE_KEY'),
                'token' => 'token'
            ])
            ->andReturn($this->mockClass(Response::class, [[
                'method' => 'json',
                'result' => false
            ]]));

        $data = $this->getJsonFixture('contact_us_request.json');

        $response = $this->post('/contact-us', $data);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testContactUsRequestCheckEmail()
    {
        MtCaptcha::shouldReceive('verify')->andReturn(true);

        $data = $this->getJsonFixture('contact_us_request.json');

        $this->post('/contact-us', $data);

        $this->assertMailEquals(ContactUsMail::class, [
            'emails' => $this->adminEmail,
            'fixture' => 'contact_us_request.html',
            'subject' => 'New contact us request',
            'cc' => $data['email']
        ]);
    }

    public function testCommissionUsRequest()
    {
        MtCaptcha::shouldReceive('verify')->andReturn(true);

        $data = $this->getJsonFixture('commission_request.json');

        $response = $this->post('/users/1/commission', $data);

        $response->assertOk();
    }

    public function testCommissionUsRequestCheckEmail()
    {
        MtCaptcha::shouldReceive('verify')->andReturn(true);

        $data = $this->getJsonFixture('commission_request.json');

        $this->post('/users/1/commission', $data);

        $this->assertMailEquals(CommissionRequestMail::class, [
            'emails' => $this->adminEmail,
            'fixture' => 'commission_request.html',
            'subject' => 'New commission request',
            'cc' => $data['email']
        ]);
    }

    public function testCommissionUsProductNotFoundRequest()
    {
        $data = $this->getJsonFixture('commission_request.json');

        $response = $this->post('/users/0/commission', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
