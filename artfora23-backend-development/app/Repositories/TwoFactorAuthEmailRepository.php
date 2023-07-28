<?php

namespace App\Repositories;

use App\Models\TwoFactorAuthEmail;
use Carbon\Carbon;

/**
 * @property TwoFactorAuthEmail $model
 */
class TwoFactorAuthEmailRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(TwoFactorAuthEmail::class);
    }

    public function filterByActuality(): self
    {
        if (!empty($this->filter['only_actual'])) {
            $this->query->where('created_at', '>=', Carbon::now()->subMinutes(10));
        }

        return $this;
    }
}
