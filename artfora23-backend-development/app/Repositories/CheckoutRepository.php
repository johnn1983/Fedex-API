<?php

namespace App\Repositories;

use App\Models\OrderItem;

/**
 * @property  OrderItem $model
 */
class CheckoutRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(OrderItem::class);
    }
}
