<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Artel\Support\Services\EntityService;
use App\Repositories\CartItemRepository;
use App\Models\CartItem;
use App\Models\Product;

/**
 * @mixin CartItemRepository
 * @property CartItemRepository $repository
 */
class CartItemService extends EntityService
    {
        public function getVatRate ($country){
            $vat_rates = [
                "Bulgaria" => 20,
                "Cyprus" => 19,
                "Czechia" => 21,
                "Germany" => 19,
                "Denmark" => 25,
                "Estonia" => 20,
                "Greece" => 24,
                "Spain" => 21,
                "Finland" => 24,
                "France" => 20,
                "Croatia" => 25,
                "Hungary" => 27,
                "Ireland" => 23,
                "Italy" => 22,
                "Lithuania" => 21,
                "Luxembourg" => 17,
                "Latvia" => 21,
                "Malta" => 18,
                "Netherlands" => 21,
                "Poland" => 23,
                "Portugal" => 23,
                "Rumania" => 19,
                "Sweden" => 25,
                "Slovenia" => 22,
                "Slovakia" => 20,
            ];
            $key = array_search($country, array_keys($vat_rates));
            if ($key !== false) {
                $vat_rate = $vat_rates[$country];
                return $vat_rate;
            } else {
                return 18;
            }
           
    }
    
    public function __construct()
    {
        $this->setRepository(CartItemRepository::class);
    }
    public function read($id){

        return CartItem::where('user_id', $id)->with(['product'])->get();
    }

    public function create($data)
    {
        $existProduct=CartItem::where('product_id',$data['product_id'])->where('user_id', $data['user_id'])->first();
        if($existProduct){
            $existProduct['quantity'] = $existProduct['quantity'] + $data['quantity'];
            $existProduct['shipping']=$data['shipping'];
            $existProduct->save();
            return $existProduct->load(['product']);
        }
        
        else{

            $data['shipping']=floatval(rand(1, 10));
            
            $product=Product::where('id',$data['product_id'])->with(['user'])->first();
            $sellerCountry=$product['user']['sel_country'];
            
            $vat=$this->getVatRate($sellerCountry);
            $data['vat']=floatval($vat);
            
            //we have to add shipping fee code when we add new cart items
            $res = $this->repository->create($data);
            return $res->load(['product']);
        }
       
    }
    public function delete($id)
    {
        $cartItem = $this->find($id);
        if ($cartItem) 
        {
            $cartItem->delete();
        }
        return;
       
    }
    
}
