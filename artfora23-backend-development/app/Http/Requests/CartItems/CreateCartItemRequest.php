<?php

namespace App\Http\Requests\CartItems;

use App\Http\Requests\Request;

class CreateCartItemRequest extends Request
{
    public function rules(): array
    {
        return [
            'user_id' => 'numeric',
            'product_id' => 'numeric',
            'quantity' => 'numeric',
            'shipping' => 'string',
        ];
    }
}