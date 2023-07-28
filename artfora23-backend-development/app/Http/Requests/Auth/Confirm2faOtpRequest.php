<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description
 * This request is needed to complete enabling of 2FA with OTP service for already registered and verified users.
 */
class Confirm2faOtpRequest extends FormRequest
{
    public function authorize()
    {
        return !empty($this->user()->email_verified_at);
    }

    public function rules()
    {
        return [
            'code' => 'required'
        ];
    }
}
