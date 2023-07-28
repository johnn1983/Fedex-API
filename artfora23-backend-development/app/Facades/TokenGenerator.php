<?php


namespace App\Facades;

use App\Services\TokenGeneratorService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin TokenGeneratorService
 */
class TokenGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'token_generator';
    }
}
