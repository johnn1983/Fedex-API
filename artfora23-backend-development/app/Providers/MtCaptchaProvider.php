<?php

namespace App\Providers;

use App\Services\MtCaptchaService;
use Illuminate\Support\ServiceProvider;

class MtCaptchaProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind('mtcaptcha', function () {
            return new MtCaptchaService();
        });
    }
}
