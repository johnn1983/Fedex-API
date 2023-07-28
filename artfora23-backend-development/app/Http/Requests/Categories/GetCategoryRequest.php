<?php

namespace App\Http\Requests\Categories;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\CategoryService;
use App\Http\Requests\Request;

class GetCategoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'with' => 'array',
            'with.*' => 'string|required',
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
