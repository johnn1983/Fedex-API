<?php

namespace App\Support;

use Illuminate\Support\Str;
use Twilio\Rest\Client;

/**
 * @property Client client
 */
class TwilioClient
{
    protected $client;
    protected $verificationSid;

    public function __construct()
    {
        $sid = config('services.twilio.account_sid');
        $token = config('services.twilio.auth_token');
        $this->client = new Client($sid, $token);

        $this->verificationSid = config('services.twilio.verification_sid');
    }

    public function verify($phone)
    {
        $phone = Str::contains('+', $phone) ? $phone : "+{$phone}";

        return $this->client->verify->v2
            ->services($this->verificationSid)
            ->verifications
            ->create($phone, 'sms')
            ->sid;
    }

    public function check($phone, $code)
    {
        $phone = Str::contains('+', $phone) ? $phone : "+{$phone}";

        $status = $this->client->verify->v2
            ->services($this->verificationSid)
            ->verificationChecks
            ->create($code, ['to' => $phone])
            ->status;

        return $status === 'approved';
    }
}
