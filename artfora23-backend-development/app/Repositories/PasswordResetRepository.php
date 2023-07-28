<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use Carbon\Carbon;

/**
 * @property PasswordReset $model
 */
class PasswordResetRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(PasswordReset::class);
    }

    public function clear()
    {
        return $this->model
            ->where('created_at', '<=', Carbon::now()->subDay())
            ->delete();
    }
}
