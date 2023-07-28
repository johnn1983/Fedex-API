<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItems\CreateCartItemRequest;
use App\Http\Requests\CartItems\DeleteCartItemRequest;
use App\Services\CartItemService;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function create(CreateCartItemRequest $request, CartItemService $service)
    {
        $data = $request->onlyValidated();

        $result = $service->create($data);

        return response()->json($result);
    }
    public function read(Request $request, CartItemService $service, $id)
    {

        $result = $service->read($id);
        $newArray = collect($result)
            ->groupBy('product.user.id')
            ->map(function ($group) {
                $totalPrice = $group->sum(function ($item) {
                    if ($item->product->is_sale_price === true) {
                        return $item->product->sale_price_in_euro * $item->quantity;
                    } else {
                        return $item->product->price_in_euro * $item->quantity;
                    }
                });
                $totalShipping = $group->sum('shipping');
                return [
                    'user_id' => $group->first()['product']['user']['id'],
                    'carts' => $group->toArray(),
                    'total_price' => $totalPrice + $totalShipping,
                    'total_shipping' => $totalShipping,
                ];
            })
            ->values()
            ->toArray();

        return response()->json($newArray);
    }
    public function delete(DeleteCartItemRequest $request, CartItemService $service, $id)
    {
        $result = $service->delete($id);
    }
}
