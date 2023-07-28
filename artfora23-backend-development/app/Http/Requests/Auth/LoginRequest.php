<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use Carbon\Carbon;
use App\Models\User;

class LoginRequest extends Request
{
    public function rules(): array
    {
        $current = Carbon::now();
        User::where('email_verified_at', '=', null)
            ->where('email_verification_token_sent_at', '<', $current->subMinutes(30))
            ->forceDelete();

        return [
            'login' => 'string|email|required',
            'password' => 'required',
        ];
    }
}
