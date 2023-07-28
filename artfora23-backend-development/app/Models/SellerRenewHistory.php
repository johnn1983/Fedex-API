<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Artel\Support\Traits\ModelTrait;

class SellerRenewHistory extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [
        'seller_id',
        'subscription_id',
        'price',
        'transaction_id',
        'price_id',
        'type',
    ];
    protected $guarded = [
        'end_date',
        'start_date ',
    ];
}
