<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\Request;
use App\Models\Product;

/**
 * @description
 * If a regular user(not admin) tries to search by products he will see only Approved products.
 * If they need to see their own products with other statuses they need to mention their `user_id` in
 * the request.
 *
 * By default, a user could see only products with visibility level lower than the value in `product_visibility_level`
 * of the user.
 * To see their own products with visibility level higher than visibility level from the profile they need to pass
 * `user_id` as it is done for categories
 *
 * Also, requesting parent category cause requesting child categories too.
 */
class SearchProductsRequest extends Request
{
    public function rules(): array
    {
        $statuses = join(',', Product::STATUSES);
        $searchQueryFields = join(',',Product::SEARCH_QUERY_FIELDS);

        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'user_id' => 'integer|exists:users,id',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id',
            'query' => 'string|nullable',
            'query_by' => "string|in:{$searchQueryFields}|nullable",
            'status' => "string|in:{$statuses}",
            'order_by' => 'string|in:created_at,random',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|required',
            'author' => 'string',
            'username' => 'string',
            'tagname' => 'string'
        ];
    }
}
