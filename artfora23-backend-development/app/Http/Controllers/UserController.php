<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\Users\GetUserRequest;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\DeleteUserRequest;
use App\Http\Requests\Users\SearchUserRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Requests\Users\GetUserProfileRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Stripe;

class UserController extends Controller
{
    public function create(CreateUserRequest $request, UserService $service)
    {
        $data = $request->onlyValidated();

        $result = $service->create($data);

        return response()->json($result);
    }

    public function get(GetUserRequest $request, UserService $service, $id)
    {
        $result = $service
            ->with(['avatar_image', 'background_image'])
            ->find($id);

        return response()->json($result);
    }

    public function update(UpdateUserRequest $request, UserService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function profile(GetUserProfileRequest $request, UserService $service)
    {
        $result = $service
            ->with(['avatar_image', 'background_image'])
            ->find($request->user()->id);

        return response()->json($result);
    }

    public function updateProfile(UpdateProfileRequest $request, UserService $service)
    {
        $result =  $service->update($request->user()->id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
        // return response()->json($result);
    }

    public function delete(DeleteUserRequest $request, UserService $service, $id)
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchUserRequest $request, UserService $service)
    {
        $result = $service->search($request->onlyValidated());
        return response()->json($result);
    }

    /**
     * Create stripe connect account and link for a user
     *
     * @param UserService $service
     * @param integer $id
     * @return json object
     */
    public function stripeConnect(UserService $service, $id)
    {
        $stripeSecretKey = config('services.stripe.secret');
        $stripeAccountLink = [];

        try {
            $userInfo = $service
                ->find($id);

            if (empty($userInfo)) {
                throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
            }
            if (isset($userInfo->stripe_account_id)) {
                // Create stripe account link
                $account_id = $userInfo->stripe_account_id;
                $account_link = $this->stripeAccoutLink($account_id, $stripeSecretKey);
            } else {
                // Create new stripe account
                $account = $this->stripeAccout($userInfo, $stripeSecretKey);
                $account_id = $account->id;
                $userInfo->stripe_account_id = $account_id;
                $userInfo->stripe_status = 'inactive';
                $userInfo->save();

                // Create new stripe account link
                $account_link = $this->stripeAccoutLink($account_id, $stripeSecretKey);
            }

            $account_url = $account_link->url;
            $stripeAccountLink['user_id'] = $id;
            $stripeAccountLink['email'] = $userInfo->email;
            $stripeAccountLink['strip_account_link'] =  $account_url;
        } catch (\Exception $e) {
            throw $e;
        }
        return response()->json($stripeAccountLink);
    }

    /**
     * Create new stripe account
     *
     * @param object $userInfo
     * @param string $stripeSecretKey
     * @return object $account
     */
    private function stripeAccout($userInfo, $stripeSecretKey)
    {
        Stripe\Stripe::setApiKey($stripeSecretKey);

        $account = Stripe\Account::create(array(
            'type' => 'express',
            "email" => $userInfo->email,
            'metadata' => ["user_id" => $userInfo->id]
        ));
        return  $account;
    }

    /**
     * Create new stripe account link
     *
     * @param int $account_id
     * @param string $stripeSecretKey
     * @return object $account_link
     */
    private function stripeAccoutLink($account_id, $stripeSecretKey)
    {
        Stripe\Stripe::setApiKey($stripeSecretKey);
        $returnUrl =  "https://dev.artfora.artel-workshop.com/";
        $refreshUrl =  "https://dev.artfora.artel-workshop.com/";

        $account_link =  Stripe\AccountLink::create([
            'account' => $account_id,
            'refresh_url' =>  $returnUrl,
            'return_url' =>   $refreshUrl,
            'type' => 'account_onboarding',
        ]);
        return $account_link;
    }
}
