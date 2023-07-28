<?php

namespace App\Http\Controllers;

use App\Models\SellerPayoutHistory;
use App\Models\SellerSubscription;
use App\Models\StripePrice;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Stripe;

class StripePaymentController extends Controller
{
    /**
     * Make a stripe payment
     *
     * @param UserService $service
     * @param Request $request
     * @return null
     */
    public function index(UserService $service, Request $request)
    {
        $stripeSecretKey = config('services.stripe.secret');
        $response['status'] = "error";
        $response['stripe_payment_url'] = "";
        $stripePaymentUrl = "";
        $userId = $request->input('user_id');
        $orderId = $request->input('order_id');
        $amount = $request->input('amount');
        try {
            $userInfo = $service
                ->find($userId);

            if (empty($userInfo)) {
                throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
            }

            if (empty((float)$amount)) {
                throw new NotFoundHttpException(__('validation.exceptions.amount_not_found', ['entity' => '']));
            }
            // Create stripe customer
            if (empty($userInfo->stripe_customer_id)) {
                $stripeCustomerId = $this->stripeCustomer($userInfo, $stripeSecretKey);
                $userInfo->stripe_customer_id = $stripeCustomerId;
                $userInfo->save();
                // create stripe session
                $stripePaymentUrl = $this->createStripeSession($userInfo, $stripeSecretKey, $orderId, $amount);
            } else {
                // create stripe session
                $stripePaymentUrl = $this->createStripeSession($userInfo, $stripeSecretKey, $orderId, $amount);
            }

            if (empty($stripePaymentUrl)) {
                throw new NotFoundHttpException(__('validation.exceptions.stripe_payment_url_not_found', ['entity' => '']));
            } else {
                $response['status'] = "success";
                $response['stripe_payment_url'] = $stripePaymentUrl;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return response()->json($response);
    }

    /**
     * Create new stripe customer
     *
     * @param object $userInfo
     * @param string $stripeSecretKey
     * @return string $customerId
     */
    private function stripeCustomer($userInfo, $stripeSecretKey)
    {
        $customerId = "";
        try {
            $stripe = new Stripe\StripeClient($stripeSecretKey);
            $customer = $stripe->customers->create([
                'name' => $userInfo->username,
                'email' => $userInfo->email,
                'description' => '',
            ]);
            if ($customer) {
                $customerId =  $customer->id;
            }
            return $customerId;
        } catch (\Exception $e) {
            return $customerId;
        }
    }

    /**
     * Create a stripe session
     *
     * @param object $userInfo
     * @param string $stripeSecretKey
     * @param integer $orderId
     * @param integer $amount
     * @return string $stripeSessionUrl
     */
    private function createStripeSession($userInfo, $stripeSecretKey, $orderId = 0, $amount, $mode = 'payment',  $priceId = "", $planType = 1)
    {
        $stripePaymentUrl = "";
        $stripeCurrency = config('services.stripe.stripe_currency');
        $stripePriceId = config('services.stripe.stripe_seller_subscription_price_id');

        try {
            if ($mode == 'subscription') {
                $paymentcart[] = [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ];
                $metaData = [
                    'user_id' => $userInfo->id,
                    'price_id' => $priceId,
                    'plan_type' => $planType,
                ];
            } else {
                $type = ($planType == 2) ? "donation" : "order";
                $paymentcart[] = [
                    'price_data' => [
                        'currency' => strtolower($stripeCurrency),
                        'unit_amount' => $amount * 100,
                        'product_data' => [
                            'name' => "ARTfora Product",
                            'images' => ["https://claimalgo.com/wp-content/uploads/2023/07/ARTforaProduct3.jpg"],
                        ],
                    ],
                    'quantity' => 1,
                ];

                $metaData = [
                    'user_id' => $userInfo->id,
                    'orderId' => $orderId,
                    'type' => $type
                ];
            }

            $stripe = new Stripe\StripeClient($stripeSecretKey);
            $sessions = $stripe->checkout->sessions->create([
                'success_url' => 'https://dev.artfora.artel-workshop.com/',
                'cancel_url' => 'https://dev.artfora.artel-workshop.com/',
                'customer' =>  $userInfo->stripe_customer_id,
                'line_items' => $paymentcart,
                'metadata' => $metaData,
                'mode' => $mode,
            ]);
            if ($sessions) {
                $stripePaymentUrl = $sessions->url;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return  $stripePaymentUrl;
    }

    /**
     * Payout to seller
     * @param UserService $service
     */
    public function stripePayout(SellerPayoutHistory $sellerService)
    {
        $stripePaymentUrl = "";
        $stripeCurrency = config('services.stripe.stripe_currency');
        $stripeSecretKey = config('services.stripe.secret');
        $stripeAfterPaymentDays = (int)config('services.stripe.stripe_seller_payment_after_order_days');
        $payAmout = 0;
        $isDone = false;
        $response['code'] = 400;
        $response['status'] = "error";
        $response['message'] = "Payment not done";

        try {
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            $filterDate = "-" . $stripeAfterPaymentDays . " days";
            $previousDate = date('Y-m-d', strtotime($filterDate));
            $sellerPayoutData = $sellerService->where(['pay_status' => 'pending'])
                ->where('order_date', '<',  $previousDate)
                ->with(['user' => function ($query) {
                    $query->select([
                        'id', 'stripe_account_id', 'stripe_status', 'stripe_customer_id', 'email', 'phone'
                    ]);
                }])
                ->get();

            if ($sellerPayoutData->isEmpty()) {
                throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Seller']));
            }
            $payAmout = collect($sellerPayoutData)->sum('total_pay_amount');
            $balance = $this->retrieveBalance($stripe);
            if ($balance < $payAmout) {
                throw new NotFoundHttpException(__('validation.exceptions.stripe_low_balance', ['entity' => '']));
            }

            foreach ($sellerPayoutData as $data) {
                $stripeAccountId = $data->user->stripe_account_id;
                $stripeStatus = $data->user->stripe_status;
                $amount = $data->total_pay_amount;
                $sellerId = $data->seller_id;
                $orderId = $data->order_id;
                $id = $data->id;
                if (!empty($stripeAccountId) && $stripeStatus == "active") {
                    $transfer =  $stripe->transfer->create([
                        "amount" => $amount * 100,
                        "currency" => $stripeCurrency,
                        "destination" => $stripeAccountId,
                        'metadata' => [
                            'seller_id' => $sellerId,
                            'order_id' => $orderId
                        ]
                    ]);
                    if ($transfer) {
                        $sellerObj = $sellerService->find($id);
                        $sellerObj->pay_status = 'successed';
                        $sellerObj->pay_transaction_id = $transfer->balance_transaction;
                        if ($sellerObj->save()) {
                            $isDone = true;
                        }
                    }
                }
            }
            if ($isDone) {
                $response['code'] = 200;
                $response['status'] = "success";
                $response['message'] = "Payment done successfully";
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return response()->json($response);
    }

    /**
     * Retrieve stripe balance
     */
    private function retrieveBalance($stripe)
    {
        try {
            $balance = $stripe->balance->retrieve([]);
            return $balance->available[0]->amount;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create seller subscription
     *
     * @param UserService $service
     * @param integer $id
     */
    public function getSubscription(UserService $userService, Request $request)
    {
        $stripeSecretKey = config('services.stripe.secret');
        $orderId = 0;
        $amount = 0;
        $response['status'] = "error";
        $response['stripe_payment_url'] = "";
        $stripePaymentUrl = "";
        try {
            $sellerId = $request->input('seller_id');
            $amount = (int)$request->input('amount');
            $planType = (int)$request->input('plan_type');

            if (!in_array($planType, [1, 2])) {
                throw new NotFoundHttpException(__('validation.exceptions.plan_type_not_found', ['entity' => '1 or 2']));
            }
            if ($amount < 1) {
                throw new NotFoundHttpException(__('validation.exceptions.amount_not_found', ['entity' => '']));
            }
            $userInfo = $userService->find($sellerId);

            if (empty($userInfo)) {
                throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
            }

            /* $subscriptionInfo = SellerSubscription::where(['stripe_status' => 'successed', 'seller_id' => $sellerId])->first();
            if(!empty($subscriptionInfo)) {
                throw new NotFoundHttpException(__('validation.exceptions.subscription_exist', ['entity' => '']));
            */

            if (empty($userInfo->stripe_customer_id)) {
                $stripeCustomerId = $this->stripeCustomer($userInfo, $stripeSecretKey);
                $userInfo->stripe_customer_id = $stripeCustomerId;
                $userInfo->save();
                // one type payment
                if ($planType == 2) {
                    // create stripe session
                    $stripePaymentUrl = $this->createStripeSession($userInfo, $stripeSecretKey, $orderId, $amount, 'payment', "", $planType);
                } else {
                    // Create stripe price ID
                    $priceId = $this->createStripePriceId($stripeSecretKey, $amount);
                    // create stripe subscription session
                    $stripePaymentUrl = $this->createStripeSession($userInfo, $stripeSecretKey, $orderId, $amount, 'subscription', $priceId);
                }
            } else {
                if ($planType == 2) {
                    // create stripe session
                    $stripePaymentUrl = $this->createStripeSession($userInfo, $stripeSecretKey, $orderId, $amount, 'payment', "", $planType);
                } else {
                    // Create stripe price ID
                    $priceId = $this->createStripePriceId($stripeSecretKey, $amount);
                    // create stripe subscription session
                    $stripePaymentUrl = $this->createStripeSession($userInfo, $stripeSecretKey, $orderId, $amount, 'subscription', $priceId);
                }
            }
            if (empty($stripePaymentUrl)) {
                throw new NotFoundHttpException(__('validation.exceptions.stripe_payment_url_not_found', ['entity' => '']));
            } else {
                $response['status'] = "success";
                $response['stripe_payment_url'] = $stripePaymentUrl;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return response()->json($response);
    }

    /**
     * Create stripe price ID
     *
     * @return string
     */
    private function createStripePriceId($stripeSecretKey, $amout = 0)
    {
        try {
            $stripeCurrency = config('services.stripe.stripe_currency');
            $stripeProductId = config('services.stripe.stripe_product_id');
            $priceObj = (new StripePrice);

            $priceInfo = $priceObj->where(['price' => $amout])->first();
            if ($priceInfo) {
                return $priceInfo->price_id;
            } else {
                $stripe = new \Stripe\StripeClient($stripeSecretKey);
                $price = $stripe->prices->create([
                    'unit_amount' => $amout * 100,
                    'currency' => $stripeCurrency,
                    'recurring' => ['interval' => 'month'],
                    'product' => $stripeProductId,
                ]);
                if ($price) {
                    $priceId = $price->id;
                    $priceObj->price_id = $priceId;
                    $priceObj->price = $amout;
                    $priceObj->type = 1;
                    $priceObj->created_at = time();
                    $priceObj->updated_at = time();
                    if ($priceObj->save()) {
                        return $priceId;
                    }
                }
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
