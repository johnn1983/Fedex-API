<?php

namespace App\Http\Requests\Fedex;

use App\Http\Requests\Request;

class AddressValidationRequest extends Request
{
    public function rules(): array
    {
        return [
            'street_lines' => 'string |array',
            'city' => 'string',
            'state' => 'string',
            'postal_code' => 'string',
            'code'=>'string'
        ];
    }
}