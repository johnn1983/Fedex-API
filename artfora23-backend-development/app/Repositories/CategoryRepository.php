<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property Category $model
 */
class CategoryRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(Category::class);
    }

    public function filterByAuthor(): self
    {
        if (isset($this->filter['author'])) {
            $this->query->whereHas('children', function ($categoryQuery) {
                $categoryQuery->where(function (Builder $subQuery) {
                    $subQuery->whereHas('products', function ($productQuery) {
                        $productQuery->where('author', $this->filter['author']);
                    });
                });
            });
        }

        return $this;
    }

    public function filterByUsername(): self
    {
        if (isset($this->filter['username'])) {
            $this->query->whereHas('children', function ($categoryQuery) {
                $categoryQuery->where(function (Builder $subQuery) {
                    $subQuery->whereHas('products', function ($productQuery) {
                        $productQuery->where(function (Builder $subQuery) {
                            $subQuery->whereHas('user', function($userQuery) {
                                $userQuery->where('username', $this->filter['username']);
                            });
                        });
                    });
                });
            });
        }

        return $this;
    }

    public function filterOnlyParents(): self
    {
        if (isset($this->filter['only_parents'])) {
            $this->query->whereNull('parent_id');
        }

        return $this;
    }
}
