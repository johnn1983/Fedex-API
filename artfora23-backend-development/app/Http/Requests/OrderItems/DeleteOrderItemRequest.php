<?php

namespace App\Http\Requests\OrderItems;

use App\Models\OrderItem;
use App\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\OrderItemService;
use App\Http\Requests\Request;

class DeleteOrderItemRequest extends Request
{
    public ?OrderItem $order_item;


   

    public function validateResolved()
    {
        $service = app(OrderItemService::class);
        $this->order_item = $service->find($this->route('id'));

        if (empty($this->order_item)) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'OrderItem']));
        }

        parent::validateResolved();
    }
}
