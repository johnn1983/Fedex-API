<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @property OrderItem $model
 */
class OrderItemRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(OrderItem::class);
    }
}
