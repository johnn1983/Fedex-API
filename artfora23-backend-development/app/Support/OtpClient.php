<?php

namespace App\Support;

use Illuminate\Support\Str;
use Twilio\Rest\Client;
use OTPHP\TOTP;

class OtpClient
{
    public function generate()
    {
        $otp = TOTP::create();

        $otp->setLabel(config('app.name'));

        return [
            'secret' => $otp->getSecret(),
            'qr_code' => $otp->getQrCodeUri()
        ];
    }

    public function check($secret, $code)
    {
        $otp = TOTP::create($secret);

        return $otp->verify($code);
    }
}
