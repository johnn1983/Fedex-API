<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CheckSms2faRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required',
            'code' => 'required'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $user = app(UserService::class)->findBy('phone', $this->input('phone'));

        if (empty($user) || ($user['2fa_type'] !== User::SMS_2FA_TYPE)) {
            throw new UnauthorizedHttpException('', message: '2FA auth through SMS is not enabled');
        }
    }
}
