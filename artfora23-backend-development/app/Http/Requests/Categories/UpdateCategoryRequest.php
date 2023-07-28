<?php

namespace App\Http\Requests\Categories;

use App\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\CategoryService;
use App\Http\Requests\Request;

class UpdateCategoryRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id === Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'title' => 'string',
            'parent_id' => 'integer|nullable|exists:categories,id'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(CategoryService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Category']));
        }
    }
}
