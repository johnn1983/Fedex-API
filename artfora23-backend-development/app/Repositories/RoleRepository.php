<?php

namespace App\Repositories;

use App\Models\Role;

/**
 * @property  Role $model
*/
class RoleRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(Role::class);
    }
}
