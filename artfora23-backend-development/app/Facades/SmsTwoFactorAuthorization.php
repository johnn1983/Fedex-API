<?php

namespace App\Facades;

use App\Support\TwilioClient;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin TwilioClient
 */
class SmsTwoFactorAuthorization extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'twilio';
    }
}
