<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\FedexController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WebhookOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['middleware' => 'auth'], function () {
    Route::post('/auth/2fa/sms/enable', ['uses' => AuthController::class . '@enableSms2fa']);
    Route::post('/auth/2fa/sms/confirm', ['uses' => AuthController::class . '@confirmSms2fa']);

    Route::post('/auth/2fa/otp/generate', ['uses' => AuthController::class . '@getOtpQrCode']);
    Route::post('/auth/2fa/otp/confirm', ['uses' => AuthController::class . '@confirmOtp2fa']);

    Route::post('/users', ['uses' => UserController::class . '@create']);
    Route::put('/users/{id}', ['uses' => UserController::class . '@update']);
    Route::delete('/users/{id}', ['uses' => UserController::class . '@delete']);
    Route::get('/users/{id}', ['uses' => UserController::class . '@get']);
    Route::get('/users', ['uses' => UserController::class . '@search']);
    Route::get('/profile', ['uses' => UserController::class . '@profile']);
    Route::put('/profile', ['uses' => UserController::class . '@updateProfile']);

    Route::post('/media', ['uses' => MediaController::class . '@create']);
    Route::delete('/media/{id}', ['uses' => MediaController::class . '@delete']);
    Route::get('/media', ['uses' => MediaController::class . '@search']);

    Route::put('/settings/{name}', ['uses' => SettingController::class . '@update']);
    Route::get('/settings/{name}', ['uses' => SettingController::class . '@get']);
    Route::get('/settings', ['uses' => SettingController::class . '@search']);

    Route::post('/categories', [CategoryController::class, 'create']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'delete']);

    Route::post('/products', [ProductController::class, 'create']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'delete']);

    Route::post('/order-item', [OrderItemController::class, 'create']);
    Route::get('/order-item', [OrderItemController::class, 'read']);
    Route::delete('/order-item/{id}', [OrderItemController::class, 'delete']);

    Route::post('/cart-item', [CartItemController::class, 'create']);
    Route::get('/cart-item/{id}', [CartItemController::class, 'read']);
    Route::delete('/cart-item/{id}', [CartItemController::class, 'delete']);
    Route::get('/checkout', [CheckoutController::class, 'checkout']);

    Route::post('/validate-postal-code', 'FedExController@validatePostalCode');
    Route::get('ship', [FedExController::class, 'ship']);
    Route::post('/ship_rate', [FedExController::class, 'shipRate']);
    Route::post('/service_availability', [FedExController::class, 'service_availability']);
    Route::get('/countries', [FedExController::class, 'countries']);
});

Route::group(['middleware' => ['guest', 'api']], function () {
    Route::post('/validate-address', [FedExController::class, 'addressValidation']);
    Route::post('/validate-postal_code', [FedExController::class, 'postalCodeValidation']);

    Route::get('/status', ['uses' => StatusController::class . '@status']);

    Route::get('/auth/refresh', ['uses' => AuthController::class . '@refreshToken'])
        ->middleware(['jwt.refresh']);
    Route::get('/auth/verify-remember-token/{remember_token}', [AuthController::class, 'verifyRememberToken']);
    Route::post('/auth/login', ['uses' => AuthController::class . '@login']);
    Route::post('/auth/register', ['uses' => AuthController::class . '@register']);
    Route::post('/auth/register/email/verify', ['uses' => AuthController::class . '@verifyEmail', 'as' => 'auth.confirm']);
    Route::post('/auth/forgot-password', ['uses' => AuthController::class . '@forgotPassword']);
    Route::post('/auth/restore-password', ['uses' => AuthController::class . '@restorePassword']);
    Route::post('/auth/restore-password/check', ['uses' => AuthController::class . '@checkRestorePasswordToken']);
    Route::post('/auth/2fa/email/resend', ['uses' => AuthController::class . '@resend2faEmail']);
    Route::post('/auth/2fa/email/check', ['uses' => AuthController::class . '@check2faEmail']);
    Route::post('/auth/2fa/sms/resend', ['uses' => AuthController::class . '@resend2faSms']);
    Route::post('/auth/2fa/sms/check', ['uses' => AuthController::class . '@check2faSms']);
    Route::post('/auth/2fa/otp/check', ['uses' => AuthController::class . '@check2faOtp']);

    Route::get('/categories/{id}', [CategoryController::class, 'get']);
    Route::get('/categories', [CategoryController::class, 'search']);

    Route::get('/texts', [TextController::class, 'search']);

    Route::get('/filters', [FilterController::class, 'search']);

    Route::get('/products/{id}', [ProductController::class, 'get']);
    Route::get('/products', [ProductController::class, 'search']);

    Route::post('/users/{id}/commission', [ContactUsController::class, 'commission']);
    Route::post('/contact-us', [ContactUsController::class, 'contactUs']);
    Route::get('/text', [AuthController::class, 'textData']);
    Route::get('/stripe-connect/{id}', [UserController::class, 'stripeConnect']);
    Route::post('order_payment', [StripePaymentController::class, 'index']);
    Route::post('webhook', [WebhookController::class, 'index']);
    Route::post('webhook-order', [WebhookOrderController::class, 'index']);
    Route::get('stripe-payout', [StripePaymentController::class, 'stripePayout']);
    Route::post('stripe-subscription', [StripePaymentController::class, 'getSubscription']);
});
