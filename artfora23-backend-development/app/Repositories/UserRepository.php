<?php

namespace App\Repositories;

use App\Models\User;

/**
 * @property  User $model
*/
class UserRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(User::class);
    }
}
