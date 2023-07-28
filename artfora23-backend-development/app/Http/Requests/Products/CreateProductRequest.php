<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\Request;
use App\Models\Product;

/**
 * @description
 * Visibility level is in interval from 0 to 3 where
 * 0 - Common level
 * 1 - Nudity
 * 2 - Erotic
 * 3 - Porno
 */
class CreateProductRequest extends Request
{
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
            'title' => 'string|required',
            'description' => 'string|required',
            'tags' => 'required_if:is_ai_safe,false',
            'visibility_level' => "integer|required|in:{$visibilityLevels}",
            'is_ai_safe' => 'boolean',
            'media' => 'array',
            'media.*' => 'integer|exists:media,id',
            'is_sale_price'=>'boolean',
            'sale_price_in_euro'=>'numeric',
            'alt_text'=>'string'
        ];
    }
}
