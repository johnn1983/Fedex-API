<?php

namespace App\Providers;

use App\Support\OtpClient;
use Illuminate\Support\ServiceProvider;

class OtpProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind('otp', function () {
            return new OtpClient();
        });
    }
}
