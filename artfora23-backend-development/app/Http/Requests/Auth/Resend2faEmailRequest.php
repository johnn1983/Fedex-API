<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Resend2faEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'string|email|required|exists:users,email'
        ];
    }

    public function validateResolved()
    {
        $user = app(UserService::class)->findBy('email', $this->input('email'));

        if (empty($user) || ($user['2fa_type'] !== User::EMAIL_2FA_TYPE)) {
            throw new UnauthorizedHttpException('', message: '2FA auth through email is not enabled');
        }

        parent::validateResolved();
    }
}
