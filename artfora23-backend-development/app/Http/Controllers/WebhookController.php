<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Stripe;
use App\Services\UserService;

class WebhookController extends BaseController
{
    /**
     * Update user account according to stripe response
     * 
     * @param UserService $service
     * @return json object 
     */
    public function index(UserService $service)
    {
        $stripeSecretKey = config('services.stripe.secret');
        $sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : "";
        Stripe\Stripe::setApiKey($stripeSecretKey);
        $endpoint_webhook_secret = config('services.stripe.webhook_secret');

        $response['status_code'] = '400';
        $response['status'] = 'error';
        $response['message'] = 'Invalid data';

        $payload = @file_get_contents('php://input');
        $postData = "";

        try {
            $sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']);
            $postData = Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_webhook_secret
            );
        } catch (Exception $e) {
            // Invalid payload
            $response['message'] = 'Invalid payload';
        } catch (Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            $response['message'] = 'Invalid signature';
        }

        try {
            if ($postData) {
                if (isset($postData->type) && isset($postData->id)) {
                    $account = $postData->account;
                    if ($postData->type == 'account.updated') {
                        $object = $postData->data->object;
                        $charges_enabled = $object->charges_enabled;
                        $user_id = (int)$object->metadata->user_id;
                        $card_payments = $object->capabilities->card_payments;
                        $transfers = $object->capabilities->transfers;
                        $details_submitted = $object->details_submitted;
                        $payouts_enabled = $object->payouts_enabled;
                        if ($charges_enabled) {

                            if ($details_submitted == true && $payouts_enabled == true && $user_id > 0) {

                                $userInfo = $service
                                    ->find($user_id);

                                if (empty($userInfo)) {
                                    $response['message'] = 'User not found';
                                } else {
                                    $userInfo->stripe_status = 'active';
                                    $userInfo->save();

                                    $response['status_code'] = '200';
                                    $response['status'] = 'success';
                                    $response['message'] = 'User data updated successfully';
                                }
                            } else {
                                $response['message'] = 'Payout not active';
                            }
                        } else {
                            $response['message'] = 'Stripe charge not enabled';
                        }
                    } else {
                        $response['message'] = 'Invalid account type';
                    }
                } else {
                    $response['message'] = 'Data not found';
                }
            } else {
                $response['message'] = 'Post data not found';
            }
        } catch (Exception $e) {
            return response()->json($response);
        }
        return response()->json($response);
    }
}
