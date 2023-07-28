<?php

namespace App\Http\Requests\Categories;

use App\Http\Requests\Request;

class SearchCategoriesRequest extends Request
{
    public function rules(): array
    {
        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'author' => 'string',
            'username' => 'string',
            'query' => 'string|nullable',
            'with.*' => 'string|required',
            'parent_id' => 'integer|nullable|exists:categories,id',
            'only_parents' => 'boolean'
        ];
    }
}
