<?php

namespace App\Facades;

use App\Services\MtCaptchaService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin MtCaptchaService
 */
class MtCaptcha extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mtcaptcha';
    }
}
