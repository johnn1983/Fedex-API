<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use App\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\ProductService;
use App\Http\Requests\Request;

/**
 * @description
 * Visibility level is in interval from 0 to 3 where
 * 0 - Common level
 * 1 - Nudity
 * 2 - Erotic
 * 3 - Porno
 */
class UpdateProductRequest extends Request
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
        $visibilityLevels = join(',', Product::VISIBILITY_LEVELS);

        return [
            'width' => 'numeric',
            'height' => 'numeric',
            'depth' => 'numeric',
            'price' => 'numeric',
            'price_in_euro' => 'numeric',
            'shipping_in_euro' => 'numeric',
            'quantity_for_sale' => 'numeric',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id',
            'weight' => 'numeric',
            'author' => 'string',
            'title' => 'string',
            'slug' => 'string',
            'description' => 'string',
            'status' => 'string',
            'tags' => 'string',
            'visibility_level' => "integer|in:{$visibilityLevels}",
            'is_ai_safe' => 'boolean',
            'media' => 'array',
            'media.*' => 'integer|exists:media,id',
            'is_sale_price' => 'boolean',
            'sale_price_in_euro' => 'numeric',
            'alt_text' => 'string'

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
