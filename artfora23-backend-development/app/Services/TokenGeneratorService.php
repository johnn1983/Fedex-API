<?php

namespace App\Services;

use Illuminate\Support\Str;

class TokenGeneratorService
{
    public function getRandom($length = 16)
    {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    public function getCode()
    {
        $code = random_int(100000, 999999);

        return Str::padLeft($code, 6, '0');
    }
}
