<?php

namespace App\Http\Requests\Text;

use App\Http\Requests\Request;

class SearchTextRequest extends Request
{
    public function rules(): array
    {
        return [
            'text_name' => 'string',
            'text_content' => 'string',
            'text_colour' => 'string',
        ];
    }
}