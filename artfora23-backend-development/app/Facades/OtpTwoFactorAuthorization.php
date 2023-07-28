<?php

namespace App\Facades;

use App\Support\OtpClient;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin OtpClient
 */
class OtpTwoFactorAuthorization extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'otp';
    }
}
