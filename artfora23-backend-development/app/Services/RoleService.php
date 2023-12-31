<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Artel\Support\Services\EntityService;

/**
 * @property RoleRepository $repository
 */
class RoleService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(RoleRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->getSearchResults();
    }
}
