<?php

namespace App\Http\Requests\Auth;

use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Confirm2faEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => 'required|string|exists:users,email_verification_token'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $user = app(UserService::class)
            ->withTrashed()
            ->findBy('email_verification_token', $this->input('token'));

        if (Carbon::parse($user['email_verification_token_sent_at']) < Carbon::now()->subDay()) {
            throw new BadRequestHttpException('Confirmation token has been expired');
        }
    }
}
