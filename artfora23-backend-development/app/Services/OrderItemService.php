<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;
use Artel\Support\Services\EntityService;
use App\Repositories\OrderItemRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\User;
/**
 * @mixin OrderItemRepository
 * @property OrderItemRepository $repository
 */
class OrderItemService extends EntityService
{
    public function __construct()
    {  
        $this->setRepository(OrderItemRepository::class);
    }
    public function read(){
        $orderItems = OrderItem::all();
        return $orderItems;
    }
    public function create($data)
    { 
        $pendingOrder = Order::where('order_status', 'checkout')->where('user_id', $data['user_id'])->first();
        $product=Product::where('id', $data['prod_id'])->first();
        $productUser=User::where('id', $product['user_id'])->first();

                // $data['slug'] = Str::slug($data['prod_title']);
        if($pendingOrder){
             $data['order_id'] = $pendingOrder['id'];
             $duplicatedItem=OrderItem::where('prod_id', $data['prod_id'])->where('order_id', $data['order_id'])->first();
             if($duplicatedItem){
                $duplicatedItem['quantity']=$duplicatedItem['quantity'] + 1;
                $duplicatedItem->save();
                return $duplicatedItem;
                

             }
             else{
                return $this->repository
                        ->create($data);
             }
             
        }
        else{
            $user = User::where('id', $data['user_id'])->first();
            $payload = [
                "user_id" => $data['user_id'],
                'vat'=>0,
                'currency'=>0,
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
                "dev_address" => $productUser['dev_address'], 
                "dev_address2" => $productUser['dev_address2'], 
                "dev_postal" => $productUser['dev_postal'], 
                "dev_city" => $productUser['dev_city'], 
                "dev_state" => $productUser['dev_state'], 
                "dev_country" => $productUser['dev_country'], 
                "dev_phone" => $productUser['dev_phone'],
                "dev_email" => $productUser['dev_email'],
                "dev_att" => $productUser['dev_att'],
                "order_status" => 'checkout', 
                "total" => $data['price']* $data['quantity'], 
                'transaction_id' => null,
                'payment_mode' => null
            ];

                Order::create($payload);

            $createdOrder = Order::where('order_status', 'checkout')->first();
            $data['order_id'] = $createdOrder['id'];
            return $this->repository
                    ->create($data);
            }
            
    }
    public function delete($id){
        OrderItem::destroy($id);
        return;

    }
}
