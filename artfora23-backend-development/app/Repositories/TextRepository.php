<?php

namespace App\Repositories;

use App\Models\Text;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property  Text $model
 */
class TextRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(Text::class);
    }

    public function getSearchResults(): LengthAwarePaginator
    {
        return parent::getSearchResults();
    }
}
