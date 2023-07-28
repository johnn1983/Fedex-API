<?php

namespace App\Repositories;

use Artel\Support\Repositories\BaseRepository;

class Repository extends BaseRepository
{
    public function setVisible($fields)
    {
        $this->model::setForceVisibleFields($fields);

        return $this;
    }
}