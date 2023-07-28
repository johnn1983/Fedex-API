<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description
 * This request starts the process of enabling of SMS two-factor authentication for already existed and verified user.
 * After this request an SMS will be sent and user need to send the code to /auth/2fa/sms/confirm
 * where the enabling of 2FA will be complete.
 */
class Enable2faSmsRequest extends FormRequest
{
    public function authorize()
    {
        return !empty($this->user()->phone) && !empty($this->user()->email_verified_at);
    }

    public function rules()
    {
        return [];
    }
}
