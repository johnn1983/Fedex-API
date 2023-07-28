<?php

namespace App\Console\Commands;

use App\Services\PasswordResetService;
use Illuminate\Console\Command;

class ClearResetPasswords extends Command
{
    protected $signature = 'password_resets:clear';

    protected $description = 'Clear all reset password records that are older than one hour';

    public function handle()
    {
        app(PasswordResetService::class)->clear();
    }
}
