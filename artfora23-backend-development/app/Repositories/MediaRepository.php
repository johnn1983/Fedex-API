<?php

namespace App\Repositories;

use App\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property  Media $model
 */
class MediaRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(Media::class);
    }

    public function getSearchResults(): LengthAwarePaginator
    {
        $this->query->applyMediaPermissionRestrictions();

        return parent::getSearchResults();
    }
}
