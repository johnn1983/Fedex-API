<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerPayoutHistory;
use App\Models\SellerSubscription;
use App\Models\SellerRenewHistory;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Stripe;


class WebhookOrderController extends BaseController
{
    /**
     * Update order table according to stripe response
     * Add/Update/Cancel Subscription
     *
     * @param Order $ordeObject
     * @param UserService $userService
     * @return json object 
     */
    public function index(Order $ordeObject, UserService $userService)
    {
        $stripeSecretKey = config('services.stripe.secret');
        $sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : "";
        Stripe\Stripe::setApiKey($stripeSecretKey);
        $endpoint_webhook_secret = config('services.stripe.webhook_secret_order');

        $response['status_code'] = '400';
        $response['status'] = 'error';
        $response['message'] = 'Invalid data';

        $payload = @file_get_contents('php://input');
        $postData = null;

        try {
            $postData = Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_webhook_secret
            );
        } catch (Exception $e) {
            // Invalid payload
            return $response['message'] = 'Invalid payload';
        } catch (Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return $response['message'] = 'Invalid signature';
        }

        try {
            if ($postData) {

                if (isset($postData->type) && isset($postData->id)) {
                    if ($postData->type == 'checkout.session.completed') {

                        $object = $postData->data->object;
                        $transactionId = $object->payment_intent;
                        $userId = (int)$object->metadata->user_id;
                        $orderId = isset($object->metadata->orderId) ? $object->metadata->orderId : "";
                        $type = isset($object->metadata->type) ? $object->metadata->type : "";
                        $status = $object->status;
                        $payment_status = $object->payment_status;
                        $mode = $object->mode;

                        /** Update order */
                        if (!empty($transactionId) && $mode == 'payment' && $type == "order" && !empty($orderId)) {
                            if ($status == "complete" && $payment_status == "paid") {
                                $orderInfo = $ordeObject
                                    ->find($orderId);
                                if (empty($orderInfo)) {
                                    $response['message'] = 'Order not found';
                                } else {
                                    $orderInfo->order_status = 'paid';
                                    $orderInfo->transaction_id = $transactionId;
                                    $orderInfo->save();

                                    if ($orderInfo) {
                                        // Check seller payout history data
                                        $payoutOrder = SellerPayoutHistory::where('order_id', $orderId)->get();
                                        if ($payoutOrder->isEmpty()) {

                                            $result = OrderItem::where('order_id', $orderId)
                                                ->with(['product' => function ($query) {
                                                    $query->select([
                                                        'id', 'price', 'user_id'
                                                    ]);
                                                }])
                                                ->get();

                                            $orderItemData = collect($result)
                                                ->groupBy('product.user_id')
                                                ->map(function ($group) {
                                                    $totalPrice = $group->sum(function ($item) {
                                                        return $item->price;
                                                    });
                                                    return [
                                                        'seller_id' => $group->first()['product']['user_id'],
                                                        'total_price' => $totalPrice,
                                                    ];
                                                })
                                                ->values()
                                                ->toArray();
                                            // Create new seller payout history data
                                            foreach ($orderItemData as $data) {
                                                $sellerPayoutHistoryObj = (new SellerPayoutHistory);
                                                $sellerPayoutHistoryObj->seller_id = $data['seller_id'];
                                                $sellerPayoutHistoryObj->order_id = $orderId;
                                                $sellerPayoutHistoryObj->total_pay_amount = $data['total_price'];
                                                $sellerPayoutHistoryObj->order_date = date("Y-m-d");
                                                $sellerPayoutHistoryObj->save();
                                            }
                                        }
                                    }
                                    $response['status_code'] = '200';
                                    $response['status'] = 'success';
                                    $response['message'] = 'Order data updated successfully';
                                }
                            } else {
                                $response['message'] = 'Payment not done';
                            }

                            /** manage subscription */
                        } elseif ($status == "complete" && $mode == 'subscription' && $payment_status == "paid" && $userId > 0) {
                            $amountTotal = $object->amount_total;
                            $amountTotal = $amountTotal / 100;
                            $invoiceId = $object->invoice;
                            $subscriptionId = $object->subscription;
                            $priceId = isset($object->metadata->price_id) ? $object->metadata->price_id : "";
                            $planType = isset($object->metadata->plan_type) ? $object->metadata->plan_type : 1;
                            $isSaved = false;

                            $userInfo = $userService->find($userId);
                            if (!empty($userInfo)) {
                                $sellerSubscriptionObj = (new SellerSubscription);
                                $sellerRenewHistoryObj = (new SellerRenewHistory);
                                $sellterInfo = $sellerSubscriptionObj->where(['seller_id' => $userId])->first();
                                $stripePrice = $this->retrieveSubscription($stripeSecretKey, $subscriptionId);
                                $currentPeriodStart = Carbon::now()->format('Y-m-d H:i:s');
                                $currentPeriodEnd = Carbon::now()->addDays(30)->format('Y-m-d H:i:s');
                                if (!empty($stripePrice)) {
                                    $currentPeriodStart = date("Y-m-d H:i:s", $stripePrice->current_period_start);
                                    $currentPeriodStart = date("Y-m-d H:i:s", $stripePrice->current_period_end);
                                }
                                // Update suscription
                                if (!empty($sellterInfo)) {
                                    $sellterInfo->subscription_id = $subscriptionId;
                                    $sellterInfo->price_id = $priceId;
                                    $sellterInfo->stripe_status = 'successed';
                                    $sellterInfo->start_date = $currentPeriodStart;
                                    $sellterInfo->end_date = $currentPeriodEnd;
                                    if ($sellterInfo->save()) {
                                        $isSaved = true;
                                        $response['status_code'] = '200';
                                        $response['status'] = 'success';
                                        $response['message'] = 'Subscription updated successfully';
                                    } else {
                                        $response['message'] = 'Subscription failed to update';
                                    }
                                } else {
                                    /** create new subscription */
                                    $sellerSubscriptionObj->seller_id = $userId;
                                    $sellerSubscriptionObj->subscription_id = $subscriptionId;
                                    $sellerSubscriptionObj->price_id = $priceId;
                                    $sellerSubscriptionObj->stripe_status = 'successed';
                                    $sellerSubscriptionObj->start_date = $currentPeriodStart;
                                    $sellerSubscriptionObj->end_date = $currentPeriodEnd;
                                    $sellerSubscriptionObj->price = $amountTotal;
                                    $sellerSubscriptionObj->type = $planType;
                                    if ($sellerSubscriptionObj->save()) {
                                        $isSaved = true;
                                        $response['status_code'] = '200';
                                        $response['status'] = 'success';
                                        $response['message'] = 'Subscription added successfully';
                                    } else {
                                        $response['message'] = 'Subscription failed to create';
                                    }
                                }

                                if ($isSaved) {
                                    /** Create renew subscription history */
                                    $sellerRenewHistoryObj->seller_id = $userId;
                                    $sellerRenewHistoryObj->subscription_id = $subscriptionId;
                                    $sellerRenewHistoryObj->price = $amountTotal;
                                    $sellerRenewHistoryObj->transaction_id = $invoiceId;
                                    $sellerRenewHistoryObj->start_date = $currentPeriodStart;
                                    $sellerRenewHistoryObj->end_date = $currentPeriodEnd;
                                    $sellerRenewHistoryObj->price_id = $priceId;
                                    $sellerRenewHistoryObj->type = $planType;
                                    if ($sellerRenewHistoryObj->save()) {
                                        // new renew history created
                                    }
                                }
                            } else {
                                $response['message'] = 'Seller not found.';
                            }

                            /** manage subscription */
                        } elseif (!empty($transactionId) && $mode == 'payment' && $type == "donation") {
                            if ($status == "complete" && $payment_status == "paid") {

                                $amountTotal = $object->amount_total;
                                $amountTotal = $amountTotal / 100;
                                $subscriptionId = $object->subscription;
                                $priceId = "";
                                $planType = 2;
                                $isSaved = false;

                                $userInfo = $userService->find($userId);
                                if (!empty($userInfo)) {
                                    $sellerSubscriptionObj = (new SellerSubscription);
                                    $sellerRenewHistoryObj = (new SellerRenewHistory);
                                    $sellterInfo = $sellerSubscriptionObj->where(['seller_id' => $userId])->first();

                                    $currentPeriodStart = Carbon::now()->format('Y-m-d H:i:s');

                                    // Update suscription
                                    if (!empty($sellterInfo)) {
                                        $sellterInfo->subscription_id = "";
                                        $sellterInfo->price_id = $transactionId;
                                        $sellterInfo->stripe_status = 'successed';
                                        $sellterInfo->start_date = $currentPeriodStart;
                                        //$sellterInfo->end_date = $currentPeriodStart;
                                        if ($sellterInfo->save()) {
                                            $isSaved = true;
                                            $response['status_code'] = '200';
                                            $response['status'] = 'success';
                                            $response['message'] = 'Subscription updated successfully';
                                        } else {
                                            $response['message'] = 'Subscription failed to update';
                                        }
                                    } else {
                                        /** create new subscription */
                                        $sellerSubscriptionObj->seller_id = $userId;
                                        $sellerSubscriptionObj->subscription_id = "";
                                        $sellerSubscriptionObj->price_id = $transactionId;
                                        $sellerSubscriptionObj->stripe_status = 'successed';
                                        $sellerSubscriptionObj->start_date = $currentPeriodStart;
                                        //$sellerSubscriptionObj->end_date = $currentPeriodStart;
                                        $sellerSubscriptionObj->price = $amountTotal;
                                        $sellerSubscriptionObj->type = $planType;
                                        if ($sellerSubscriptionObj->save()) {
                                            $isSaved = true;
                                            $response['status_code'] = '200';
                                            $response['status'] = 'success';
                                            $response['message'] = 'Subscription added successfully';
                                        } else {
                                            $response['message'] = 'Subscription failed to create';
                                        }
                                    }

                                    if ($isSaved) {
                                        /** Create renew subscription history */
                                        $sellerRenewHistoryObj->seller_id = $userId;
                                        $sellerRenewHistoryObj->subscription_id = "";
                                        $sellerRenewHistoryObj->price = $amountTotal;
                                        $sellerRenewHistoryObj->transaction_id = $transactionId;
                                        $sellerRenewHistoryObj->start_date = $currentPeriodStart;
                                        $sellerRenewHistoryObj->end_date = $currentPeriodStart;
                                        $sellerRenewHistoryObj->price_id = $transactionId;
                                        $sellerRenewHistoryObj->type = $planType;
                                        if ($sellerRenewHistoryObj->save()) {
                                            // new renew history created
                                        }
                                    }
                                } else {
                                    $response['message'] = 'Seller not found';
                                }
                            } else {
                                $response['message'] = 'Payment not found';
                            }
                        } else {
                            $response['message'] = 'Transaction id or payment mode not found';
                        }
                    } elseif ($postData->type == 'customer.subscription.deleted') {
                        /** Cancel the subscription */

                        $sellerSubscriptionObj = (new SellerSubscription);
                        $object = $postData->data->object;
                        $dataObj = $object->items->data;
                        $paymentStatus = $object->status;
                        $objectType = $object->object;
                        $billingReason = $object->cancellation_details->reason;
                        $customerId = $object->customer;
                        $subscriptionId = isset($dataObj[0]->subscription) ? $dataObj[0]->subscription : "";
                        if ($objectType == 'subscription' && $paymentStatus == 'canceled') {
                            $sellterInfo = $sellerSubscriptionObj->where(['subscription_id' => $subscriptionId])
                                ->first();
                            // Update suscription
                            if (!empty($sellterInfo)) {
                                $sellterInfo->stripe_status = 'canceled';
                                if ($sellterInfo->save()) {
                                    $isSaved = true;
                                    $response['status_code'] = '200';
                                    $response['status'] = 'success';
                                    $response['message'] = 'Subscription status changed successfully';
                                } else {
                                    $response['message'] = 'Subscription failed to update';
                                }
                            } else {
                                $response['message'] = 'Seller not found';
                            }
                        } else {
                            $response['message'] = 'Invalid status type';
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
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
        return response()->json($response);
    }

    /**
     * Get subsctiption info
     * @param string $stripeSecretKey
     * @param string $subscriptionId
     */
    private function retrieveSubscription($stripeSecretKey, $subscriptionId)
    {
        try {
            $stripe = new Stripe\StripeClient($stripeSecretKey);
            $price = $stripe->subscriptions->retrieve(
                $subscriptionId,
                []
            );
            return $price;
        } catch (\Exception $e) {
            return null;
        }
    }
}
