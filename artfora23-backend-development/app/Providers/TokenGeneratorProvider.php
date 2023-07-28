<?php

namespace App\Providers;

use App\Services\TokenGeneratorService;
use Illuminate\Support\ServiceProvider;

class TokenGeneratorProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind('token_generator', function () {
            return new TokenGeneratorService();
        });
    }
}
