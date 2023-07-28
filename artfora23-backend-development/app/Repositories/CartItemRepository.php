<?php

namespace App\Repositories;

use App\Models\CartItem;

/**
 * @property  CartItem $model
 */
class CartItemRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(CartItem::class);
    }
}
