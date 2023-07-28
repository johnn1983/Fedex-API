<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property  Setting $model
*/
class SettingRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(Setting::class);
    }

    public function getSearchResults(): LengthAwarePaginator
    {
        $this->query->applySettingPermissionRestrictions();

        return parent::getSearchResults();
    }
}
