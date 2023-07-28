<?php

namespace App\Services;

use App\Repositories\FilterRepository;
use Artel\Support\Services\EntityService;
use Illuminate\Support\Arr;
use App\Models\Filter;
use Illuminate\Support\Facades\DB;

/**
 * @property FilterRepository $repository
 */
class FilterService extends EntityService
{

    public function __construct()
    {
        $this->setRepository(FilterRepository::class);
    }

    public function search($filters)
    {
        if (Arr::get($filters, 'order_by') === 'random') {
            $filters['order_by'] = DB::raw('random()');
        }

        $queryBy = Arr::get($filters, 'query_by', Filter::SEARCH_QUERY_FIELDS);

        return $this
            ->with(Arr::get($filters, 'with', []))
            ->withCount(Arr::get($filters, 'with_count', []))
            ->searchQuery($filters)
            ->getSearchResults();
    }
}
