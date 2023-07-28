<?php

namespace App\Http\Requests\Checkouts;

use App\Http\Requests\Request;

class CheckoutRequest extends Request
{
    public function rules(): array
    {
        return [
        'cardNumber' => 'numeric',
        'expireMonth' => 'numeric',
        'expireYear' => 'numeric',
        'cvc'=>'numeric',
        ];
    }
}