<?php

namespace App\Http\Requests\Fedex;

use App\Http\Requests\Request;

class ShippingRateRequest extends Request
{
    public function rules(): array
    {
        return [
            'product_id' => 'integer',
            'count' => 'integer',
        ];
    }
}
