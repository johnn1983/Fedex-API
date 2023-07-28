<?php

namespace App\Http\Requests\Fedex;

use App\Http\Requests\Request;

class PostalCodeValidationRequest extends Request
{
    public function rules(): array
    {
        return [
            'postal_code' => 'string',
            'country_code'=>'string',
            'ship_date'=>'date_format:YY-MM-DD',
            'state'=>'string'
          
        ];
    }
}