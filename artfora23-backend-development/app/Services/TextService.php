<?php

namespace App\Services;

use App\Repositories\TextRepository;
use Artel\Support\Services\EntityService;
use Illuminate\Support\Arr;
use App\Models\Text;
use Illuminate\Support\Facades\DB;

/**
 * @property TextRepository $repository
 */
class TextService extends EntityService
{

    public function __construct()
    {
        $this->setRepository(TextRepository::class);
    }

    public function search($filters)
    {
        if (Arr::get($filters, 'order_by') === 'random') {
            $filters['order_by'] = DB::raw('random()');
        }

        $queryBy = Arr::get($filters, 'query_by', Text::SEARCH_QUERY_FIELDS);

        // return $this
        //     ->with(Arr::get($filters, 'with', []))
        //     ->withCount(Arr::get($filters, 'with_count', []))
        //     ->searchQuery($filters)
        //     ->getSearchResults();
        $records =Text::all();
        return $records;
    }
}
