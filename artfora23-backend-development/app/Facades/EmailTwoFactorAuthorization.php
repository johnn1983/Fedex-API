<?php

namespace App\Facades;

use App\Services\TwoFactorAuthEmailService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin TwoFactorAuthEmailService
 */
class EmailTwoFactorAuthorization extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'email_2fa';
    }
}
