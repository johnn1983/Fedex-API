<?php

namespace App\Http\Requests\OrderItems;

use App\Http\Requests\Request;
use App\Models\OrderItem;

/**
 * @description
 * Visibility level is in interval from 0 to 3 where
 * 0 - Common level
 * 1 - Nudity
 * 2 - Erotic
 * 3 - Porno
 */
class CreateOrderItemRequest extends Request
{
    public function rules(): array
    {
        return [
            "prod_id"=>'numeric', 
            "prod_title"=>"string",
            "prod_artist"=>'string',
            "prod_height"=>'numeric | nullable',
            "prod_width"=>'numeric | nullable',
            "prod_depth"=>'numeric | nullable',
            "prod_weight"=>'numeric | nullable',
            "quantity"=>'numeric', 
            "price"=>'numeric',
            'user_id'=>'numeric', 
        ];
    }
}
