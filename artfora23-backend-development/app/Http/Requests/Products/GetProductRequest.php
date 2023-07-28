<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use App\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\ProductService;
use App\Http\Requests\Request;

class GetProductRequest extends Request
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

        $service = app(ProductService::class);

        $product = $service->find($this->route('id'));

        if (empty($product)) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Product']));
        }

        if ($this->isAdminOrOwner($product)) {
            return;
        }

        if ($product->status !== Product::APPROVED_STATUS) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Product']));
        }
    }

    protected function isAdminOrOwner(Product $product)
    {
        if (empty($this->user())) {
            return false;
        }

        return ($this->user()->role_id === Role::ADMIN) || ($this->user()->id === $product->user_id);
    }
}
