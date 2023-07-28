<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Services\UserService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Carbon\Carbon;

class RegisterUserRequest extends Request
{
    public function rules(): array
    {
        $current = Carbon::now();
        User::where('email_verified_at', '=', null)
            ->where('email_verification_token_sent_at', '<', $current->subMinutes(30))
            ->forceDelete();

        return [
            'username' => 'required|string',
            'tagname' => 'required|string|unique:users,tagname',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|same:confirm',
            'confirm' => 'required|string',
            'redirect_after_verification'  => 'string'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $user = app(UserService::class)
            ->withTrashed()
            ->findBy('email', $this->input('email'));

        if (empty($user)) {
            return;
        }

        if (empty($user['email_verified_at'])) {
            return;
        }

        throw new BadRequestHttpException('Email has already been taken');
    }
}
