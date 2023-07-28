<?php

namespace App\Providers;

use App\Support\TwilioClient;
use Illuminate\Support\ServiceProvider;

class TwilioProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind('twilio', function () {
            return new TwilioClient();
        });
    }
}
