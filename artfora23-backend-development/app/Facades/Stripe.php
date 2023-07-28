<?php


namespace App\Facades;

use App\Services\StripeService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin StripeService
 */
class Stripe extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stripe';
    }
}
