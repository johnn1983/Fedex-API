<?php

namespace App\Repositories;

use App\Models\Filter;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property  Filter $model
 */
class FilterRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(Filter::class);
    }

    public function getSearchResults(): LengthAwarePaginator
    {
        return parent::getSearchResults();
    }
}
