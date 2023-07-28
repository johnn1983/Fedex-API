<?php

namespace App\Services;

use App\Repositories\PasswordResetRepository;
use Artel\Support\Services\EntityService;
use App\Models\PasswordReset;

/**
 * @property PasswordReset $model
 */
class PasswordResetService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(PasswordResetRepository::class);
    }

    public function search($filters)
    {
        return $this->searchQuery($filters)
        ->filterByQuery(['email', 'token'])
        ->getSearchResults();
    }
}
