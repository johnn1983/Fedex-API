<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description
 * This request is needed to generate QR code to show it to user. A user will use this QR code in OTP application
 * to use it in future to login by OTP.
 */
class GetOtpQrCodeRequest extends FormRequest
{
    public function authorize()
    {
        return !empty($this->user()->email_verified_at);
    }

    public function rules()
    {
        return [];
    }
}
