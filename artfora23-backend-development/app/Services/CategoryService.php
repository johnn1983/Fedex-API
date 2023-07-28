<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Artel\Support\Services\EntityService;
use App\Repositories\CategoryRepository;

/**
 * @mixin CategoryRepository
 * @property CategoryRepository $repository
 */
class CategoryService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(CategoryRepository::class);
    }

    public function search($filters)
    {
        return $this
            ->with(Arr::get($filters, 'with', []))
            ->withCount(Arr::get($filters, 'with_count', []))
            ->searchQuery($filters)
            ->filterByQuery(['title'])
            ->filterOnlyParents()
            ->filterByAuthor()
            ->filterByUsername()
            ->getSearchResults();
    }
}
