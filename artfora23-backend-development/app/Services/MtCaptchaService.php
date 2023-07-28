<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MtCaptchaService
{
    public function verify($token)
    {
        $secret = config('services.mtcaptcha.secret');

        $response = Http::get('https://service.mtcaptcha.com/mtcv1/api/checktoken', [
            'privatekey' => $secret,
            'token' => $token
        ]);

        return $response->json('success');
    }
}