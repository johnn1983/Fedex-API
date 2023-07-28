<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Artel\Support\Services\EntityService;
// use App\Repositories\CheckoutRepository;
use App\Models\Order;
use App\Models\User;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Product;

/**
 * @mixin CheckoutRepository
 */
//  * @property CheckoutRepository $repository
class CheckoutService extends EntityService
{
    public function __construct()
    {
        // $this->setRepository(CheckoutRepository::class);
    }

    public function checkout($data, $id)
    {

        $checkoutOrder = Order::where('order_status', 'checkout')->where('user_id', $id)->first();
        $cartItems = CartItem::where('user_id', $id)->with(['product'])->get();

        if ($checkoutOrder) {
            for ($i = 0; $i < count($cartItems); $i++) {
                OrderItem::create([
                    "order_id" => $checkoutOrder['id'],
                    "product_title" => $cartItems[$i]['product']['title'],
                    "product_artist" => $cartItems[$i]['product']['author'],
                    "product_width" => $cartItems[$i]['product']['width'],
                    "product_height" => $cartItems[$i]['product']['height'],
                    "product_weight" => $cartItems[$i]['product']['weight'],
                    "product_depth" => $cartItems[$i]['product']['depth'],
                    "price" => $cartItems[$i]['product']['is_sale_price'] === true ? $cartItems[$i]['product']['sale_price_in_euro'] : $cartItems[$i]['product']['price_in_euro'],
                    "quantity" => $cartItems[$i]['quantity'],
                    "product_id" => $cartItems[$i]['product_id'],
                    "shipping" => $cartItems[$i]['shipping'],
                ]);
                $product = Product::where('id', $cartItems[$i]['product_id'])->first();
                $product['quantity_for_sale'] -= $cartItems[$i]['quantity'];
                $product->save();
                $cartItems[$i]->delete();
            };
            return ($checkoutOrder['id']);
        } else {
            $totalPrice = 0;
            $totalShipping = 0;

            for ($i = 0; $i < count($cartItems); $i++) {
                $totalPrice += $cartItems[$i]['product']['price_in_euro'] * $cartItems[$i]['quantity'];
                $totalShipping += $cartItems[$i]['shipping'] * $cartItems[$i]['quantity'];
            };


            $user = User::where('id', $id)->first();
            $payload = [];
            if ($user['dev_email']) {
                $payload = [
                    "user_id" => $id,
                    'vat' => 0,
                    'currency' => 'EUR',
                    "shipping" => $totalShipping,
                    "inv_address" => $user['inv_address'],
                    "inv_address2" => $user['inv_address2'],
                    "inv_postal" => $user['inv_postal'],
                    "inv_city" => $user['inv_city'],
                    "inv_state" => $user['inv_state'],
                    "inv_country" => $user['inv_country'],
                    "inv_phone" => $user['inv_phone'],
                    "inv_email" => $user['inv_email'],
                    "inv_att" => $user['inv_att'],
                    "dev_address" => $user['dev_address'],
                    "dev_address2" => $user['dev_address2'],
                    "dev_postal" => $user['dev_postal'],
                    "dev_city" => $user['dev_city'],
                    "dev_state" => $user['dev_state'],
                    "dev_country" => $user['dev_country'],
                    "dev_phone" => $user['dev_phone'],
                    "dev_email" => $user['dev_email'],
                    "dev_att" => $user['dev_att'],
                    "order_status" => 'checkout',
                    "total" => $totalPrice,
                    'transaction_id' => null,
                    'payment_mode' => null
                ];
            } else {
                $payload = [
                    "user_id" => $id,
                    'vat' => 0,
                    'currency' => 'EUR',
                    "shipping" => 0,
                    "inv_address" => $user['inv_address'],
                    "inv_address2" => $user['inv_address2'],
                    "inv_postal" => $user['inv_postal'],
                    "inv_city" => $user['inv_city'],
                    "inv_state" => $user['inv_state'],
                    "inv_country" => $user['inv_country'],
                    "inv_phone" => $user['inv_phone'],
                    "inv_email" => $user['inv_email'],
                    "inv_att" => $user['inv_att'],
                    "dev_address" => $user['inv_address'],
                    "dev_address2" => $user['inv_address2'],
                    "dev_postal" => $user['inv_postal'],
                    "dev_city" => $user['inv_city'],
                    "dev_state" => $user['inv_state'],
                    "dev_country" => $user['inv_country'],
                    "dev_phone" => $user['inv_phone'],
                    "dev_email" => $user['inv_email'],
                    "dev_att" => $user['inv_att'],
                    "order_status" => 'checkout',
                    "total" => $totalPrice,
                    'transaction_id' => null,
                    'payment_mode' => null
                ];
            }

            Order::create($payload);
            $currentOrder = Order::where('order_status', 'checkout')->where('user_id', $id)->first();
            for ($i = 0; $i < count($cartItems); $i++) {
                OrderItem::create([
                    "order_id" => $currentOrder['id'],
                    "product_title" => $cartItems[$i]['product']['title'],
                    "product_artist" => $cartItems[$i]['product']['author'],
                    "product_width" => $cartItems[$i]['product']['width'],
                    "product_height" => $cartItems[$i]['product']['height'],
                    "product_weight" => $cartItems[$i]['product']['weight'],
                    "product_depth" => $cartItems[$i]['product']['depth'],
                    "price" => $cartItems[$i]['product']['is_sale_price'] === true ? $cartItems[$i]['product']['sale_price_in_euro'] : $cartItems[$i]['product']['price_in_euro'],
                    "quantity" => $cartItems[$i]['quantity'],
                    "product_id" => $cartItems[$i]['product_id'],
                    "shipping" => $cartItems[$i]['shipping'],
                ]);
                $product = Product::where('id', $cartItems[$i]['product_id'])->first();
                $product['quantity_for_sale'] -= $cartItems[$i]['quantity'];
                $product->save();
                $cartItems[$i]->delete();
            }
            return $currentOrder['id'];
        }
    }
}
