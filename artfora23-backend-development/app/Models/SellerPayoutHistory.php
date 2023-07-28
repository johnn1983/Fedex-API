<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Artel\Support\Traits\ModelTrait;

class SellerPayoutHistory extends Model
{
    use HasFactory;
    use ModelTrait;
    protected $fillable = [
        "order_id",
        "seller_id",
        "order_date",
        "total_pay_amount",
        "pay_date",
        "pay_status",
        "pay_transaction_id",    
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }
}
