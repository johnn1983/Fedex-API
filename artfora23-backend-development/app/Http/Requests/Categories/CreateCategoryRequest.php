<?php

namespace App\Http\Requests\Categories;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateCategoryRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id === Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'parent_id' => 'integer|nullable|exists:categories,id'
        ];
    }
}
