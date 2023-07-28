<?php

namespace App\Http\Requests\Filter;

use App\Http\Requests\Request;

class SearchFilterRequest extends Request
{
    public function rules(): array
    {
        return [
            'filter' => 'string',
        ];
    }
}