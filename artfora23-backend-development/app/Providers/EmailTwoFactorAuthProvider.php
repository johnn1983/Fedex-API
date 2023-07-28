<?php

namespace App\Providers;

use App\Services\TwoFactorAuthEmailService;
use Illuminate\Support\ServiceProvider;

class EmailTwoFactorAuthProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind('email_2fa', function () {
            return new TwoFactorAuthEmailService();
        });
    }
}
