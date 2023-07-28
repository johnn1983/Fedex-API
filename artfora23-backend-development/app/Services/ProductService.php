<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;
use Artel\Support\Services\EntityService;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @mixin ProductRepository
 * @property ProductRepository $repository
 */
class ProductService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(ProductRepository::class);
    }

    public function create($data)
    {
        $data['slug'] = Str::slug($data['title']);

        return $this->repository
            ->with(['media'])
            ->with(['categories'])
            ->create($data);
    }

    public function update($where, $data)
    {
        return $this->repository
            ->with(['media'])
            ->update($where, $data);
    }

    public function search($filters)
    {
        if (Arr::get($filters, 'order_by') === 'random') {
            $filters['order_by'] = DB::raw('random()');
        }

        $queryBy = Arr::get($filters, 'query_by', Product::SEARCH_QUERY_FIELDS);

        return $this
            ->with(Arr::get($filters, 'with', []))
            ->withCount(Arr::get($filters, 'with_count', []))
            ->searchQuery($filters)
            ->filterBy('user_id')
            ->filterBy('status')
            ->filterBy('is_ai_safe')
            ->filterBy('author')
            ->filterBy('user.username', 'username')
            ->filterBy('user.tagname', 'tagname')
            ->filterByQuery(Arr::wrap($queryBy))
            ->filterByStatus()
            ->filterByCategory()
            ->getSearchResults();
    }
}
