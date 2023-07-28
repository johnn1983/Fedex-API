<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use App\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\ProductService;
use App\Http\Requests\Request;

class DeleteProductRequest extends Request
{
    protected Product | null $product;

    public function authorize(): bool
    {
        if ($this->user()->role_id === Role::ADMIN) {
            return true;
        }

        if ($this->user()->id !== $this->product->user_id) {
            return false;
        }

        return true;
    }
    

    public function rules(): array
    {
        return [
            'force' => 'boolean'
        ];
    }

    public function validateResolved()
    {
        $service = app(ProductService::class);
        $this->product = $service->find($this->route('id'));

        if (empty($this->product)) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Product']));
        }

        parent::validateResolved();
    }
}
